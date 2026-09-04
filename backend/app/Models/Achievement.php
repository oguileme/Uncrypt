<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    //
    protected $table = 'achievements';

    protected $fillable = ['name', 'description', 'xp_reward', 'required_count'];

    
}
