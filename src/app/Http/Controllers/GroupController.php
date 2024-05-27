<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\InviteUsersRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use App\Notifications\InvitationApproved;
use App\Notifications\InvitationInGroup;
use App\Notifications\RequestApproved;
use App\Notifications\RequestToJoinGroup;
use App\Notifications\RoleChanged;
use App\Notifications\UserRemovedFromGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GroupController extends Controller
{
    public function profile(Request $request, Group $group)
    {
        $group->load('currentUserGroup');

        $userId = Auth::id();

        $groupMembers = GroupUser::where('group_id', $group->id)->count();

        if ($group->hasApprovedUser($userId)) {
            $posts = Post::postsForTimeline($userId, false)
                ->leftJoin('groups AS g', 'g.pinned_post_id', 'posts.id')
                ->where('group_id', $group->id)
                ->orderBy('g.pinned_post_id', 'desc')
                ->orderBy('posts.created_at', 'desc')
                ->paginate(5);

            $posts = PostResource::collection($posts);
        } else {
            return Inertia::render('Group/View', [
                'success' => session('success'),
                'group' => new GroupResource($group),
                'groupMembers' => $groupMembers,
                'posts' => null,
                'users' => [],
                'requests' => [],
                'photos' => []
            ]);
        }

        if ($request->wantsJson()) {
            return $posts;
        }

        $users = User::query()
            ->select(['users.*', 'gu.role', 'gu.status', 'gu.group_id'])
            ->join('group_users AS gu', 'gu.user_id', 'users.id')
            ->orderBy('users.name')
            ->where('status', GroupUserStatus::APPROVED->value)
            ->where('gu.group_id', $group->id)
            ->get();

        $photos = PostAttachment::query()
            ->select('post_attachments.*')
            ->join('posts AS p', 'p.id', 'post_attachments.post_id')
            ->where('p.group_id', $group->id)
            ->where('mime', 'like', 'image/%')
            ->latest()
            ->get();

        $request = $group->pendingUsers()
            ->orderBy('name')
            ->get();

        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group),
            'posts' => $posts,
            'groupMembers' => $groupMembers,
            'users' => GroupUserResource::collection($users),
            'requests' => UserResource::collection($request),
            'photos' => PostAttachmentResource::collection($photos),
        ]);
    }

    public function store(StoreGroupRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $group = Group::create($data);

        $groupUserData = [
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id()
        ];

        GroupUser::create($groupUserData);
        $group->status = $groupUserData['status'];
        $group->role = $groupUserData['role'];

        return response(new GroupResource($group), 201);
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());

        return back()->with('success', 'Группа обновлена');
    }

    public function updateImage(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response('У Вас нет доступа к группе', 403);
        }

        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'thumbnail' => ['nullable', 'image'],
        ]);

        /** @var UploadedFile $thumbnail */
        $thumbnail = $data['thumbnail'] ?? null;

        /** @var UploadedFile $cover */
        $cover = $data['cover'] ?? null;

        $success = '';

        if ($cover) {
            if ($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }
            $path = $cover->store('group-' . $group->id, 'public');
            $group->update(['cover_path' => Storage::url($path)]);
            $success = 'Обложка группы обновлена.';
        }

        if ($thumbnail) {
            if ($group->thumbnail_path) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }
            $path = $thumbnail->store('group-' . $group->id, 'public');
            $group->update(['thumbnail_path' => Storage::url($path)]);
            $success = 'Иконка группы обновлена.';
        }

        return back()->with('success', $success);
    }

    public function inviteUsers(InviteUsersRequest $request, Group $group)
    {
        $user = $request->user;

        $groupUser = $request->groupUser;

        $groupUser?->delete();

        $hours = 24;

        $token = Str::random(256);

        GroupUser::create([
            'status' => GroupUserStatus::PENDING->value,
            'role' => GroupUserRole::USER->value,
            'token' => $token,
            'token_expire_date' => Carbon::now()->addHours($hours),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => Auth::id(),
        ]);

        $user->notify(new InvitationInGroup($group, $hours, $token));

        return back()->with('success', 'Пользователю отправлено приглашение вступить в группу');
    }

    public function approveInvitation(string $token)
    {
        $groupUser = GroupUser::query()
            ->where('token', $token)
            ->first();

        $errorTitle = '';

        if (!$groupUser) {
            $errorTitle = 'Приглашение не действительно';
        } elseif ($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value) {
            $errorTitle = 'Приглашение уже использовано';
        } elseif ($groupUser->token_expire_date < Carbon::now()) {
            $errorTitle = 'Срок приглашение закончился';
        }

        if ($errorTitle !== '' && $errorTitle !== '0') {
            return \inertia('Error', ['errorTitle' => $errorTitle]);
        }

        $groupUser->status = GroupUserStatus::APPROVED->value;
        $groupUser->token_used = Carbon::now();
        $groupUser->save();

        $adminUser = $groupUser->adminUser;
        $adminUser->notify(new InvitationApproved($groupUser->group, $groupUser->user));

        return redirect(route('group.profile', $groupUser->group))
            ->with('success', 'Вы успешно вступили в группу "' . $groupUser->group->name . '"');
    }

    public function join(Group $group)
    {
        $user = \request()->user();

        $status = GroupUserStatus::APPROVED->value;
        $success = 'Вы вступили в группу "' . $group->name . '"';

        if (!$group->auto_approval) {
            $status = GroupUserStatus::PENDING->value;

            Notification::send($group->adminUsers, new RequestToJoinGroup($group, $user));
            $success = 'Запрос принят. Вас уведомят об одобрении';
        }

        GroupUser::create([
            'status' => $status,
            'role' => GroupUserRole::USER->value,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => $user->id,
        ]);

        return back()->with('success', $success);
    }

    public function approveRequest(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response('У Вас нет доступа к группе', 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'action' => ['required', Rule::enum(GroupUserStatus::class)]
        ]);

        $groupUser = GroupUser::where('user_id', $data['user_id'])
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::PENDING->value)
            ->first();

        if ($groupUser) {
            $approved = 'отклонен';

            if ($data['action'] === 'approved') {
                $approved = 'одобрен';
                $groupUser->status = GroupUserStatus::APPROVED->value;
            } else {
                $groupUser->status = GroupUserStatus::REJECTED->value;
            }
            $groupUser->save();

            $user = $groupUser->user;

            $user->notify(new RequestApproved($groupUser->group, $user, $approved));

            return back()->with('success', 'Пользователь "' . $user->name . '" ' . $approved);
        }

        return back();
    }

    public function changeRole(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response('У Вас нет доступа к группе', 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'role' => ['required', Rule::enum(GroupUserRole::class)]
        ]);

        $userId = $data['user_id'];
        if ($group->isOwner($userId)) {
            return response('Невозможно изменить роль владельца группы', 403);
        }

        $groupUser = GroupUser::where('user_id', $userId)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {

            $groupUser->role = $data['role'];
            $groupUser->save();

            $groupUser->user->notify(new RoleChanged($groupUser->group, $data['role']));
        }

        return back();
    }

    public function removeUser(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response('У Вас нет доступа к группе', 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
        ]);

        $userId = $data['user_id'];
        if ($group->isOwner($userId)) {
            return response('Невозможно удалить владельца группы', 403);
        }

        $groupUser = GroupUser::where('user_id', $userId)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $user = $groupUser->user;
            $groupUser->delete();

            $user->notify(new UserRemovedFromGroup($group));
        }

        return back();
    }
}
