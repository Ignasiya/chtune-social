<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function show()
    {
        $notifications = Auth::user()
            ->unreadNotifications()
            ->paginate(10);

        return response([
            'notifications' => NotificationResource::collection($notifications)
        ], 200);
    }

    public function update(Request $request, string $notificationId = null)
    {
        if (!$notificationId) {
            return back();
        }

        $notification = Auth::user()->unreadNotifications()->find($notificationId);
        $notification->update(['read_at' => now()]);

        return back();
    }
}
