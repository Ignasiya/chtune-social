<?php

namespace Database\Factories;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GroupUserFactory extends Factory
{
    protected $model = GroupUser::class;

    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(array_column(GroupUserStatus::cases(), 'value')),
            'role' => fake()->randomElement(array_column(GroupUserRole::cases(), 'value')),
            'token' => Str::random(256),
            'token_expire_date' => now()->addHours(24),
            'user_id' => User::factory(),
            'group_id' => Group::factory(),
            'created_by' => User::factory(),
        ];
    }
}
