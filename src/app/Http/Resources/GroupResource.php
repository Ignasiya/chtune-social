<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupResource extends JsonResource
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
            'slug' => $this->slug,
            'status' => $this->currentUserGroup?->status,
            'role' => $this->currentUserGroup?->role,
            'thumbnail_url' => $this->thumbnail_path ? Storage::url($this->thumbnail_path) : '/image/no-thumbnail.png',
            'cover_url' => $this->cover_path ? Storage::url($this->cover_path) : '/image/cover_default.jpg',
            'auto_approval' => $this->auto_approval,
            'about' => $this->about,
            'description' => $this->removeTags($this->about),
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function removeTags($string): string
    {
        $plainText = Str::take($string, 150);

        $plainText = preg_replace('/(>|<\/li>)/', '$1 ', $plainText);
        $plainText = preg_replace('/<\/p>(?!\s*<\/ul>|<p>)/', '</p> ', $plainText);
        $plainText = preg_replace('/<\/ul>(?!\s*<p>)/', '</ul> ', $plainText);

        return strip_tags($plainText);
    }
}
