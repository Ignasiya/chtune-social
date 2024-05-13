<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $uniqueNumber = fake()->numberBetween(1, 70);

        return [
            'name' => fake()->firstName() . ' ' . fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => fake()->dateTimeBetween('-1 years'),
            'password' => Hash::make(fake()->password()),
            'remember_token' => Str::random(10),
            'avatar_path' => 'https://i.pravatar.cc/300?img=' . $uniqueNumber,
        ];
    }

    public function withUsername(): UserFactory
    {
        return $this->state(function (array $attributes) {
            $user = User::create($attributes);
            $slugOptions = $user->getSlugOptions();
            $user->username = $slugOptions->generate($user->name);
            $user->save();

            return $user->toArray();
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
