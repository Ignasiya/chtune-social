<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Service\GetPostsByFollowersAndGroupService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(
        GetPostsByFollowersAndGroupService $getPostsByFollowersAndGroup,
        Request $request
    ): AnonymousResourceCollection|Response {
        $posts = $getPostsByFollowersAndGroup(Auth::id());

        $posts = PostResource::collection($posts);

        if ($request->wantsJson()) {
            return $posts;
        }

        return Inertia::render('Home', [
            'posts' => $posts,
        ]);
    }
}
