<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function show(Request $request)
    {
        $notifications = Notification::query()
            ->where('user_id', '=', Auth::id())
            ->latest()
            ->paginate(5);

        $notifications = NotificationResource::collection($notifications);

        if ($request->wantsJson()) {
            return $notifications;
        }

        return response([
            'count_notifications' => Auth::user()->countNotifications,
            'notifications' => $notifications
        ]);
    }

    public function update()
    {

    }
}
