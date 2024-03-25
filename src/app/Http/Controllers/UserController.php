<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use App\Notifications\FollowUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function followUser(Request $request, User $user)
    {
        $data = $request->validate([
            'follow' => ['boolean']
        ]);

        if ($data['follow']) {
            $message = 'Вы подписались на пользователя "' . $user->name . '"';
            Follower::create([
                'user_id' => $user->id,
                'follower_id' => Auth::id()
            ]);
        } else {
            $message = 'Вы отписались от пользователя "' . $user->name . '"';
            Follower::query()
                ->where('user_id', $user->id)
                ->where('follower_id', Auth::id())
                ->delete();
        }

        $user->notify(new FollowUser(Auth::getUser(), $data['follow']));

        return back()->with('success', $message);
    }
}
