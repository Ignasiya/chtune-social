<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $posts = Post::postsForTimeline($userId)
            ->whereHas('user.followers', function ($query) use ($userId) {
                $query->where('followers.follower_id', $userId);
            })
            ->orWhereHas('group.users', function ($query) use ($userId) {
                $query->where('group_users.user_id', $userId)
                    ->where('group_users.status', GroupUserStatus::APPROVED->value);
            })
            ->whereNot('posts.user_id', $userId)
            ->paginate(10);

//        $posts = Post::postsForTimeline($userId)
//            ->leftJoin('followers AS f', function ($join) use ($userId) {
//                $join->on('f.user_id', '=', 'posts.user_id')
//                    ->where('f.follower_id', '=', $userId);
//            })
//            ->leftJoin('group_users AS gu', function ($join) use ($userId) {
//                $join->on('gu.group_id', '=', 'posts.group_id')
//                    ->where('gu.user_id', '=', $userId)
//                    ->where('gu.status', GroupUserStatus::APPROVED->value);
//            })
//            ->whereNotNull('f.follower_id')
//            ->orWhereNotNull('gu.group_id')
//            ->whereNot('posts.user_id', '=', $userId)
//            ->paginate(10);

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }

        return Inertia::render('Home', [
            'posts' => $posts,
        ]);
    }
}
