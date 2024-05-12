<?php

namespace Database\Seeders;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Enums\ReactionEnum;
use App\Models\Comment;
use App\Models\Follower;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Пользователи
        User::factory()->create([
            'name' => 'Иван Иванов',
            'email' => 'test@example.com',
            'password' => Hash::make('12345Cs!')
        ]);

        User::factory(99)->create();

        $users = User::all();

        // Подписчики
        foreach ($users as $user) {
            $followers = $users->filter(function ($follower) use ($user) {
                return $follower->id !== $user->id;
            })->random(fake()->numberBetween(1, 10));

            $followerData = $followers->map(function ($follower) use ($user) {
                return [
                    'user_id' => $user->id,
                    'follower_id' => $follower->id,
                ];
            })->toArray();

            Follower::insert($followerData);
        }

        // Группы
        Group::factory(20)->create([
            'user_id' => function () use ($users) {
                return $users->random();
            },
        ])->each(function ($group) use ($users) {

            $groupUsers = [
                [
                    'user_id' => $group->user_id,
                    'group_id' => $group->id,
                    'role' => GroupUserRole::ADMIN->value,
                    'status' => GroupUserStatus::APPROVED->value,
                    'token' => null,
                    'token_expire_date' => null,
                    'token_used' => null,
                    'created_by' => $group->user_id,
                ]
            ];

            $usersRandom = $users->random(fake()->numberBetween(1, 15));

            foreach ($usersRandom as $user) {
                if ($group->user_id !== $user->id) {
                    $groupUsers[] = [
                        'user_id' => $user->id,
                        'group_id' => $group->id,
                        'role' => fake()->randomElement(array_column(GroupUserRole::cases(), 'value')),
                        'status' => $group->auto_approval ? GroupUserStatus::APPROVED->value : fake()->randomElement(array_column(GroupUserStatus::cases(), 'value')),
                        'token' => $user->status === GroupUserStatus::PENDING->value ? Str::random(32) : null,
                        'token_expire_date' => $user->status === GroupUserStatus::PENDING->value ? now()->addDays() : null,
                        'token_used' => $user->status === GroupUserStatus::APPROVED->value ? now() : null,
                        'created_by' => $group->user_id,
                    ];
                }
            }

            GroupUser::insert($groupUsers);
        });

        $groups = Group::all();

        // Посты
        Post::factory(100)->create([
            'user_id' => function () use ($users) {
                return $users->random();
            },
            'group_id' => function () use ($groups) {
                if (rand(1, 10) <= 7) {
                    return $groups->random();
                }
                return null;
            },
        ])->each(function ($post) use ($users, $groups) {

            $this->createComments($post, null, $users, rand(1, 2));

            $this->createReactions($post, $users);
        });
    }

    /**
     * Создает комментарий под постом или комментарием
     * @param $post
     * @param $commentParent
     * @param $users
     * @param int $numComments
     * @param bool $isFirstRecursion
     * @return void
     */
    private function createComments($post, $commentParent, $users, int $numComments, bool $isFirstRecursion = true): void
    {

        for ($i = 0; $i < $numComments; $i++) {
            $comment = Comment::factory()->make([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
                'parent_id' => $commentParent ? $commentParent->id : $post->id,
            ]);

            $comment->save();

            if ($isFirstRecursion) {
                $this->createComments($post, $comment, $users, rand(1, 3), false);
            }

            $this->createReactions($comment, $users);
        }
    }

    /**
     * Создает реакции на пост или коммент
     * @param $object
     * @param $users
     * @return void
     */
    private function createReactions($object, $users): void
    {
        $numReactions = rand(1, 3);
        $usedUsers = collect();

        for ($i = 0; $i < $numReactions; $i++) {
            $user = $users->diff($usedUsers)->random();
            $usedUsers->push($user);

            Reaction::factory()->create([
                'object_id' => $object->id,
                'object_type' => get_class($object),
                'type' => fake()->randomElement(array_column(ReactionEnum::cases(), 'value')),
                'user_id' => $user->id,
            ]);
        }
    }
}
