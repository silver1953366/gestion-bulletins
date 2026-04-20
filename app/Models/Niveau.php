<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    protected $fillable = [
        'code'
    ];

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }
}