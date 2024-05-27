<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    public const null UPDATED_AT = null;

    protected $fillable = ['user_id', 'follower_id'];
}
