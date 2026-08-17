<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Phrase extends Model
{
    //
    protected $table = 'phrase';
    protected $fillable = ['phrase'];

    public function getRandom(): self
    {
        return self::inRandomOrder()->firstOrfail();
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }
}
