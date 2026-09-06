<?php

namespace App\Models;

use Database\Factories\TypeEncryptonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeEncrypton extends Model
{
    //
    use HasFactory;

    protected $table = 'type_encryption';

    protected $fillable = ['name', 'description', 'difficulty'];

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }
}