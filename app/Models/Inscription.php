<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    protected $fillable = [
        'etudiant_id',
        'classe_id',
        'annee_academique_id',
        'statut'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }
}