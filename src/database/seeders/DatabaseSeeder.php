<?php

namespace Database\Seeders;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Enums\ReactionEnum;
use App\Models\Comment;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
        $hero = User::factory()->create([
            'name' => 'Иван Иванов',
            'email' => 'test@example.com',
            'password' => Hash::make('12345Cs!')
        ]);

        $users = User::factory(99)->create();
        $users->push($hero);

        // Подписчики
        $users->each(function ($user) use ($users) {
            $followers = $users->except($user->id)->random(fake()->numberBetween(1, 10));

            $user->followers()->attach($followers->pluck('id'));
        });

        // Группы
        $groups = Group::factory(80)
            ->make(['user_id' => fn($attributes) => $users->random()->id])
            ->each(function ($group) use ($users) {
                $group->save();

                $groupUsers = $this->generateGroupUsers($group, $users);

                $group->users()->attach($groupUsers);
            });

        // Посты
        $posts = Post::factory(200)
            ->make([
                'user_id' => fn($attributes) => $users->random()->id,
                'group_id' => fn($attributes) => $this->getRandomGroupId($attributes['user_id']),
                ])
            ->each(function ($post) use ($users) {
                $post->save();

                $usersForComment = $post->group_id ? Group::find($post->group_id)->users()->get() : $users;

                $this->createComments($post, $usersForComment, fake()->numberBetween(2, 4));
                $this->createReactions($post, $usersForComment);
            });
    }

    /**
     * С 70% шансом добавляет к посту группу
     * @param int $user_id
     * @return int|null
     */
    private function getRandomGroupId(int $user_id): ?int
    {
        $user = User::find($user_id);
        $groups = $user->groups()->get();

        if ($groups->isEmpty() || fake()->boolean(30)) {
            return null;
        }

        return $groups->random()->id;
    }

    /**
     * Наполняет таблицу groups и group_users
     * @param Group $group
     * @param Collection $users
     * @return array[]
     */
    private function generateGroupUsers(Group $group, Collection $users): array
    {
        $groupUsers = [
            [
                'group_id' => $group->id,
                'user_id' => $group->user_id,
                'role' => GroupUserRole::ADMIN->value,
                'status' => GroupUserStatus::APPROVED->value,
                'token' => null,
                'token_expire_date' => null,
                'token_used' => null,
                'created_by' => $group->user_id,
            ]
        ];

        $randomUsers = $users->except($group->user_id)->random(fake()->numberBetween(5, 15));

        foreach ($randomUsers as $user) {
            $groupUsers[] = [
                'group_id' => $group->id,
                'user_id' => $user->id,
                'role' => fake()->randomElement(array_column(GroupUserRole::cases(), 'value')),
                'status' => $group->auto_approval ? GroupUserStatus::APPROVED->value : fake()->randomElement(array_column(GroupUserStatus::cases(), 'value')),
                'token' => $user->status === GroupUserStatus::PENDING->value ? Str::random(32) : null,
                'token_expire_date' => $user->status === GroupUserStatus::PENDING->value ? now()->addDays() : null,
                'token_used' => $user->status === GroupUserStatus::APPROVED->value ? now() : null,
                'created_by' => $group->user_id,
            ];
        }

        return $groupUsers;
    }

    /**
     * Создает комментарий под постом или комментарием
     * @param Post $post
     * @param Comment|null $commentParent
     * @param Collection $users
     * @param int $numComments
     * @param bool $isFirstRecursion
     * @return void
     */
    private function createComments(
        Post $post, Collection $users,
        int $numComments,
        Comment $commentParent = null,
        bool $isFirstRecursion = true): void
    {
        for ($i = 0; $i < $numComments; $i++) {
            $comment = Comment::factory()->make([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
                'parent_id' => $commentParent?->id,
            ]);

            $comment->save();

            if ($isFirstRecursion) {
                $this->createComments($post, $users, rand(2, 5), $comment, false);
            }

            $this->createReactions($comment, $users);
        }
    }

    /**
     * Создает реакции на пост или коммент
     * @param Post|Comment $object
     * @param Collection $users
     * @return void
     */
    private function createReactions(Post|Comment $object, Collection $users): void
    {
        $usedUsers = collect();

        for ($i = 0; $i < rand(2, 5); $i++) {
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
