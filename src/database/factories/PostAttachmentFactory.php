<?php

namespace Database\Factories;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostAttachmentFactory extends Factory
{
    protected $model = PostAttachment::class;

    public function definition(): array
    {
        $post = Post::factory();
        $extension = fake()->randomElement(StorePostRequest::$extensions);
        $path = $this->faker->word . '.' . $extension;

        Storage::fake('public');
        $file = UploadedFile::fake()->create($path, 0, $extension);

        return [
            'post_id' => $post,
            'name' => $file->getClientOriginalName(),
            'path' => $file->store('attachments/' . $post->id, 'public'),
            'mime' => $extension,
            'created_by' => User::factory(),
        ];
    }
}
