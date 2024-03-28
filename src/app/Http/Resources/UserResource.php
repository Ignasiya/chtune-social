<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'update_at' => $this->update_at,
            'username' => $this->username,
            'pinned_post_id' => $this->pinned_post_id,
            'cover_url' => $this->cover_path ? Storage::url($this->cover_path) : '/image/cover_default.jpg',
            'avatar_url' => $this->avatar_path ? Storage::url($this->avatar_path) : '/image/no-avatar.png',
        ];
    }
}
