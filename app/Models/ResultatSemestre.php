<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatSemestre extends Model
{
    protected $table = 'resultats_semestres';

    protected $fillable = [
        'etudiant_id',
        'semestre_id',
        'annee_academique_id',
        'moyenne',
        'credits_total',
        'valide'
    ];

    protected $casts = [
        'moyenne' => 'decimal:2',
        'credits_total' => 'integer',
        'valide' => 'boolean'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public function isValide()
    {
        return $this->valide;
    }
}