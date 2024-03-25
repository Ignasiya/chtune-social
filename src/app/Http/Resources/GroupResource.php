<?php

namespace App\Http\Resources;

use DOMDocument;
use DOMXPath;
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
            'description' => $this->about ? Str::words($this->removeTags($this->about), 7) : '',
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function removeTags($string): string
    {
        $dom = new DOMDocument();

        $options = libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8'));
        libxml_use_internal_errors($options);

        $xpath = new DOMXPath($dom);

        $nodes = $xpath->query("//text()[not(ancestor::script) and not(ancestor::style)]");

        $plainText = '';
        foreach ($nodes as $node) {
            $plainText .= $node->nodeValue . ' ';
        }
        return trim($plainText);
    }
}
