<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @method static create(array $array)
 * @method static numOfReactions(Comment $comment)
 * @method static reactionUserComment(int $userId, Comment $comment)
 */
class Reaction extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = ['object_id', 'object_type', 'user_id', 'type'];

    /**
     * Получить родительскую модель object (поста или комментария).
     */
    public function object(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeReactionUserComment(Builder $query, int $userId, Comment $comment): Builder
    {
        return $query->where('user_id', $userId)
            ->where('object_id', $comment->id)
            ->where('object_type', Comment::class);
    }

    public function scopeNumOfReactions(Builder $query, Comment $comment): Builder
    {
        return $query->where('object_id', $comment->id)
            ->where('object_type', Comment::class);
    }
}
