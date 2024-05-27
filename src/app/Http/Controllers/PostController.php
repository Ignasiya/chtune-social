<?php

namespace App\Http\Controllers;

use App\Http\Enums\ReactionEnum;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\PostCreated;
use App\Notifications\PostDeleted;
use App\Notifications\ReactionOnPost;
use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function view(Post $post)
    {
        $post->loadCount('reactions');
        $post->load([
            'comments' => function ($query) {
                $query->withCount('reactions');
            },
        ]);
        return inertia('Post/View', [
            'post' => new PostResource($post)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @throws Exception
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        DB::beginTransaction();
        $allFilePaths = [];
        try {
            $post = Post::create($data);

            /** @var UploadedFile[] $files */
            $files = $data['attachments'] ?? [];

            $allFilePaths = $this->processAttachments($post, $files, $user);

            DB::commit();

            $group = $post->group;

            if ($group) {
                $users = $group->approvedUsers()
                    ->where('users.id', '!=', $user->id)
                    ->get();

                Notification::send($users, new PostCreated($post, $user, $group));
            }

            $followers = $user->followers;
            Notification::send($followers, new PostCreated($post, $user));

        } catch (Exception $e) {

            $this->deleteFailedAttachments($allFilePaths);
            DB::rollBack();
            throw $e;
        }
        return back();
    }

    /**
     * Update the specified resource in storage.
     * @throws Exception
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $user = $request->user();

        DB::beginTransaction();
        $allFilePaths = [];

        try {
            $data = $request->validated();
            $post->update($data);

            $deleted_ids = $data['deleted_file_ids'] ?? [];

            $attachments = PostAttachment::query()
                ->where('post_id', $post->id)
                ->whereIn('id', $deleted_ids)
                ->get();

            foreach ($attachments as $attachment) {
                $attachment->delete();
            }

            /** @var UploadedFile[] $files */
            $files = $data['attachments'] ?? [];

            $allFilePaths = $this->processAttachments($post, $files, $user);

            DB::commit();
        } catch (Exception $e) {

            $this->deleteFailedAttachments($allFilePaths);
            DB::rollBack();
            throw $e;
        }

        return back();
    }

    public function downloadAttachment(PostAttachment $attachment)
    {
        return response()->download(Storage::disk('public')->path($attachment->path), $attachment->name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $id = Auth::id();

        if ($post->isOwner($id) || $post->group && $post->group->isAdmin($id)) {
            $post->delete();

            if (!$post->isOwner($id)) {
                $post->user->notify(new PostDeleted($post->group));
            }
            return back();
        }
        return response('Отсутствует доступ для удаления записи', 403);
    }

    public function postReaction(Request $request, Post $post)
    {
        $data = $request->validate([
            'reaction' => [Rule::enum(ReactionEnum::class)],
        ]);

        $userId = Auth::id();
        $reaction = Reaction::query()
            ->where('user_id', $userId)
            ->where('object_id', $post->id)
            ->where('object_type', Post::class)
            ->first();

        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'object_id' => $post->id,
                'object_type' => Post::class,
                'user_id' => $userId,
                'type' => $data['reaction']
            ]);

            if (!$post->isOwner($userId)) {
                $user = User::where('id', $userId)->first();
                $post->user->notify(new ReactionOnPost($post, $user));
            }
        }

        $reactions = Reaction::query()
            ->where('object_id', $post->id)
            ->where('object_type', Post::class)
            ->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
        ]);
    }

    private function processAttachments(Post $post, $files, $user)
    {
        $allFilePaths = [];

        foreach ($files as $file) {
            $path = $file->store('attachments/' . $post->id, 'public');
            $allFilePaths[] = $path;

            PostAttachment::create([
                'post_id' => $post->id,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'created_by' => $user->id
            ]);
        }
        return $allFilePaths;
    }

    private function deleteFailedAttachments($allFilePaths)
    {
        foreach ($allFilePaths as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    public function fetchUrlPreview(Request $request)
    {
        $data = $request->validate([
            'url' => 'url'
        ]);

        $url = $data['url'];
        $content = file_get_contents($url);
        $document = new DOMDocument();

        libxml_use_internal_errors(true);
        $document->loadHTML($content);
        libxml_use_internal_errors(false);

        $ogTags = [];
        $metas = $document->getElementsByTagName('meta');

        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');

            if (str_starts_with($property, 'og:')) {
                $ogTags[$property] = $meta->getAttribute('content');
            }
        }
        return $ogTags;
    }

    public function pinUnpin(Request $request, Post $post)
    {
        $forGroup = $request->get('forGroup', false);
        $group = $post->group;
        $user = $request->user();

        if ($forGroup && !$group) {
            return response('Неверный запрос', 400);
        }

        if ($forGroup && !$group->isAdmin(Auth::id())) {
            return response('Не доступа для закрепления записей', 403);
        }

        $pinned = false;

        if ($forGroup && $group->isAdmin(Auth::id())) {
            if ($group->pinned_post_id === $post->id) {
                $group->pinned_post_id = null;
            } else {
                $pinned = true;
                $group->pinned_post_id = $post->id;
            }
            $group->save();
        }

        if (!$forGroup) {
            if ($user->pinned_post_id === $post->id) {
                $user->pinned_post_id = null;
            } else {
                $pinned = true;
                $user->pinned_post_id = $post->id;
            }
            $user->save();
        }
        return back()->with('success', 'Запись успешно ' . ($pinned ? 'закреплена' : 'откреплена'));
    }
}
