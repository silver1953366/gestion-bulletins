<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnneeAcademique extends Model
{
    protected $table = 'annees_academiques';
    
    protected $fillable = [
        'libelle',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function resultatsSemestres()
    {
        return $this->hasMany(ResultatSemestre::class);
    }

    public function resultatsAnnuel()
    {
        return $this->hasMany(ResultatAnnuel::class);
    }

    public function bulletins()
    {
        return $this->hasMany(Bulletin::class);
    }
}