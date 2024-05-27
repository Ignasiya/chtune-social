<?php

namespace Database\Factories;

use App\Http\Enums\ReactionEnum;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReactionFactory extends Factory
{
    protected $model = Reaction::class;

    public function definition(): array
    {
        return [
            'object_id' => Post::factory(),
            'object_type' => Post::class,
            'type' => fake()->randomElement(array_column(ReactionEnum::cases(), 'value')),
            'user_id' => User::factory(),
        ];
    }
}
