<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $groups = $user->groups()->orderByPivot('role')->orderBy('name', 'desc')->get();

        return Inertia::render('Activity', [
            'followings' => UserResource::collection($user->followings),
            'groups' => GroupResource::collection($groups)
        ]);
    }
}
