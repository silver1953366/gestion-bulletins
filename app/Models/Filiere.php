<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    protected $fillable = [
        'nom',
        'departement_id'
    ];

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }
}