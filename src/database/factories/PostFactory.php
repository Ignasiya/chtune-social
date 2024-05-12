<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'body' => fake()->paragraph(),
            'user_id' => User::factory(),
            'group_id' => Group::factory(),
        ];
    }
}
