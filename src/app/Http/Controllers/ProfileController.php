<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Follower;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index(Request $request, User $user)
    {
        $isUserFollower = false;
        if (!Auth::guest()) {
            $isUserFollower = Follower::query()
                ->where('user_id', $user->id)
                ->where('follower_id', Auth::id())
                ->exists();
        }

        $followerCount = Follower::where('user_id', $user->id)->count();

        $posts = Post::postsForTimeline(Auth::id())
            ->where('user_id', $user->id)
            ->paginate(5);

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }

        $followers = $user->followers;
        $followings = $user->followings;

        return Inertia::render('Profile/View', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'success' => session('success'),
            'isUserFollower' => $isUserFollower,
            'followerCount' => $followerCount,
            'user' => new UserResource($user),
            'followers' => UserResource::collection($followers),
            'followings' => UserResource::collection($followings),
            'posts' => $posts
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile', $request->user())->with('success', 'Профиль обновлен.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateImage(Request $request)
    {
        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'avatar' => ['nullable', 'image'],
        ]);

        $user = $request->user();

        /** @var UploadedFile $avatar */
        $avatar = $data['avatar'] ?? null;

        /** @var UploadedFile $cover */
        $cover = $data['cover'] ?? null;

        $success = '';

        if ($cover) {
            if ($user->cover_path) {
                Storage::disk('public')->delete($user->cover_path);
            }
            $path = $cover->store('user-' . $user->id, 'public');
            $user->update(['cover_path' => $path]);
            $success = 'Ваша обложка обновлена.';
        }

        if ($avatar) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $avatar->store('user-' . $user->id, 'public');
            $user->update(['avatar_path' => $path]);
            $success = 'Ваш аватар обновлен.';
        }

        return back()->with('success', $success);
    }
}
