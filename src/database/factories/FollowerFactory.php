<?php

namespace Database\Factories;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowerFactory extends Factory
{
    protected $model = Follower::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'follower_id' => User::factory(),
        ];
    }
}
