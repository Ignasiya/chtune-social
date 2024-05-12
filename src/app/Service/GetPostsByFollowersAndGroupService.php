<?php

namespace App\Service;

use App\Http\Enums\GroupUserStatus;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetPostsByFollowersAndGroupService
{
    /**
     * Get timeline posts for the given user ID by followers and group.
     *
     * @param  int  $userId
     * @return LengthAwarePaginator
     */
    public function __invoke(int $userId): LengthAwarePaginator
    {
        return Post::postsForTimeline($userId)
            ->whereHas('user.followers', function ($query) use ($userId) {
                $query->where('followers.follower_id', $userId);
            })
            ->orWhereHas('group.users', function ($query) use ($userId) {
                $query->where('group_users.user_id', $userId)
                    ->where('group_users.status', GroupUserStatus::APPROVED->value);
            })
            ->whereNot('posts.user_id', $userId)
            ->paginate(10);
    }

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

}
