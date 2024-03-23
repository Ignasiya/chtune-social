<?php

namespace Database\Factories;

use App\Models\PostAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PostAttachmentFactory extends Factory
{
    protected $model = PostAttachment::class;

    public function definition(): array
    {
        return [
            'post_id' => $this->faker->randomNumber(),
            'name' => $this->faker->name(),
            'path' => $this->faker->word(),
            'mime' => $this->faker->word(),
            'created_by' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
        ];
    }
}
