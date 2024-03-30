<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request, ?string $search = null)
    {
        if (!$search) {
            return redirect(route('home'));
        }

        $users = User::query()
            ->where('name', 'like', "%$search%")
            ->orWhere('username', 'like', "%$search%")
            ->latest()
            ->get();

        $groups = Group::query()
            ->where('name', 'like', "%$search%")
            ->orWhere('about', 'like', "%$search%")
            ->latest()
            ->get();

        $posts = Post::postsForTimeline(Auth::id())
            ->where('body', 'like', "%$search%")
            ->latest()
            ->paginate(10);

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }

        return inertia("Search", [
            'posts' => $posts,
            'search' => $search,
            'users' => UserResource::collection($users),
            'groups' => GroupResource::collection($groups)
        ]);
    }

    public function searchFollowers(?string $search = null)
    {
        if (!$search) {
            return response([
                'followers' => UserResource::collection(Auth::user()->followers)]);
        }

        $followers = User::query()
            ->select('users.*')
            ->join('followers as f', 'f.follower_id', '=', 'users.id')
            ->where('f.user_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%");
            })
            ->get();

        return response([
            'followers' => UserResource::collection($followers)
        ]);
    }

    public function searchFollowings(?string $search = null)
    {
        if (!$search) {
            return response([
                'followings' => UserResource::collection(Auth::user()->followings)]);
        }

        $followings = User::query()
            ->select('users.*')
            ->join('followers as f', 'f.user_id', '=', 'users.id')
            ->where('f.follower_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%");
            })
            ->get();

        return response([
            'followings' => UserResource::collection($followings)
        ]);
    }


    public function searchUsersInGroup(Group $group, ?string $search = null)
    {
        $query = User::query()
            ->select(['users.*', 'gu.role', 'gu.status', 'gu.group_id'])
            ->join('group_users AS gu', 'gu.user_id', 'users.id')
            ->orderBy('users.name')
            ->where('status', GroupUserStatus::APPROVED->value)
            ->where('gu.group_id', $group->id);

        if (!$search) {
            return response([
                'users' => UserResource::collection($query->get())
            ]);
        }

        $users = $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%");
            });

        return response([
            'users' => UserResource::collection($users->get())
        ]);
    }
}
