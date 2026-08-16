<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeEncrypton extends Model
{
    //
    protected $table = 'type_encryption';

    protected $fillable = ['name', 'description'];

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }
}
