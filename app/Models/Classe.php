<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $fillable = [
        'nom',
        'filiere_id',
        'niveau_id',
        'annee_universitaire'
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function semestres()
    {
        return $this->hasMany(Semestre::class);
    }
}