<?php

namespace App\Models;

use Database\Factories\AchievementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    //
    use HasFactory;

    protected $table = 'achievements';

    protected $fillable = ['name', 'description', 'xp_reward', 'required_count'];

    
}
