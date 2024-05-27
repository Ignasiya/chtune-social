<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property mixed $path
 * @property mixed $name
 */
class PostAttachment extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'post_id',
        'name',
        'path',
        'mime',
        'size',
        'created_by'
    ];

    #[\Override]
    protected static function boot(): void
    {
        parent::boot();

        static::deleted(function (self $model) {
            Storage::disk('public')->delete($model->path);
        });
    }
}
