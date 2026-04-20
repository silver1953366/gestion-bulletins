<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    protected $fillable = [
        'etudiant_id',
        'annee_academique_id',
        'type',
        'fichier_pdf',
        'generated_at'
    ];

    protected $casts = [
        'generated_at' => 'datetime'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public function getPdfUrlAttribute()
    {
        return $this->fichier_pdf ? asset('storage/' . $this->fichier_pdf) : null;
    }
}