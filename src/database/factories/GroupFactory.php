<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'cover_path' => $this->faker->word(),
            'thumbnail_path' => $this->faker->word(),
            'auto_approval' => $this->faker->boolean(),
            'about' => $this->faker->word(),
            'user_id' => $this->faker->randomNumber(),
            'deleted_by' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'approvedUsers' => $this->faker->word(),
            'adminUsers' => $this->faker->word(),
            'pendingUsers' => $this->faker->word(),
        ];
    }
}
