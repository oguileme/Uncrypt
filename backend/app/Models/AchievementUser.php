<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchievementUser extends Model
{
    //
    protected $table = 'achievements_users';

    protected $fillable = ['user_id', 'achievement_id', 'progress', 'is_completed'];
}
