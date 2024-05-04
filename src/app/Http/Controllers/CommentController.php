<?php

namespace App\Http\Controllers;

use App\Http\Enums\ReactionEnum;
use App\Http\Requests\CommentReactionRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\CommentCreated;
use App\Notifications\CommentDeleted;
use App\Notifications\PostCreated;
use App\Notifications\PostDeleted;
use App\Notifications\ReactionOnComment;
use App\Notifications\ReactionOnPost;
use App\Service\GetCommentReactionService;
use DOMDocument;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): Application|Response|ResponseFactory
    {
        $data = $request->validated();

        $comment = Comment::create([
            'post_id' => $post->id,
            'comment' => nl2br($data['comment']),
            'user_id' => Auth::id(),
            'parent_id' => $data['parent_id'] ?: null
        ]);

        $post = $comment->post;

        $post->user->notify(new CommentCreated($post, $comment));

        return response(new CommentResource($comment), 201);
    }

    public function destroy(Comment $comment): Application|Response|ResponseFactory
    {
        $id = Auth::id();
        $post = $comment->post;

        if ($comment->isOwner($id) || $post->isOwner($id)) {
            $comment->delete();

            if (!$comment->isOwner($id)) {
                $comment->user->notify(new CommentDeleted($comment, $post));
            }

            return response('', 204);
        }
        return response('Не доступа для удаления комментария', 403);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $data = $request->validated();

        $comment->update([
            'comment' => nl2br($data['comment'])
        ]);

        return new CommentResource($comment);
    }

    public function commentReaction(
        GetCommentReactionService $getCommentReactionService,
        CommentReactionRequest $request, Comment $comment): Response
    {
        $data = $request->validated();
        $userId = Auth::id();

        $reaction = $getCommentReactionService($comment, $userId, $data['reaction']);

        if ($reaction['current_user_has_reaction'] && !$comment->isOwner($userId)) {
            $user = User::where('id', $userId)->first();
            $comment->user->notify(new ReactionOnComment($comment->post, $comment, $user));
        }

        return response($reaction);
    }
}
