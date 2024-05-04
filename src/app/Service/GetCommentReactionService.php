<?php

namespace App\Service;

use App\Http\Enums\ReactionEnum;
use App\Models\Comment;
use App\Models\Reaction;

class GetCommentReactionService
{
    /**
     * Обрабатывает реакцию пользователя на комментарий.
     *
     * @param Comment $comment Экземпляр модели Comment, на который реагирует пользователь.
     * @param int $userId Идентификатор пользователя, который реагирует на комментарий.
     * @param string $reaction Тип реакции пользователя (из перечисления ReactionEnum).
     *
     * @return array Массив с результатами обработки реакции:
     *               - num_of_reactions (int): общее количество реакций на данный комментарий.
     *               - current_user_has_reaction (bool): имеет ли текущий пользователь реакцию на данный комментарий.
     *               - reaction_created (bool): была ли создана новая реакция в рамках этого вызова.
     */
    public function __invoke(Comment $comment, int $userId, string $reaction): array
    {
        $existingReaction = Reaction::query()
            ->where('user_id', $userId)
            ->where('object_id', $comment->id)
            ->where('object_type', Comment::class)
            ->first();

        if ($existingReaction) {
            $existingReaction->delete();
            $reactionCreated = false;

        } else {
            Reaction::create([
                'object_id' => $comment->id,
                'object_type' => Comment::class,
                'user_id' => $userId,
                'type' => $reaction,
            ]);
            $reactionCreated = true;
        }

        $numOfReactions = Reaction::query()
            ->where('object_id', $comment->id)
            ->where('object_type', Comment::class)
            ->count();

        return [
            'num_of_reactions' => $numOfReactions,
            'current_user_has_reaction' => $reactionCreated,
        ];
    }
}
