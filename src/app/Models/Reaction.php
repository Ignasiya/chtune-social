<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @method static create(array $array)
 */
class Reaction extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = ['object_id', 'object_type', 'user_id', 'type'];

    /**
     * Получить родительскую модель object (поста или комментария).
     */
    public function object(): MorphTo
    {
        return $this->morphTo();
    }
}
