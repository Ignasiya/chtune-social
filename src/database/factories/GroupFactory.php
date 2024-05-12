<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'auto_approval' => fake()->boolean(),
            'about' => fake()->sentence(),
            'user_id' => User::factory(),
        ];
    }

    public function withSlug(): GroupFactory
    {
        return $this->state(function (array $attributes) {
            $group = Group::create($attributes);
            $slugOptions = $group->getSlugOptions();
            $group->slug = $slugOptions->generate($group->name);
            $group->save();

            return $group->toArray();
        });
    }
}
