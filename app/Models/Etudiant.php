<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Etudiant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'bac',
        'provenance'
    ];

    protected $casts = [
        'date_naissance' => 'date:Y-m-d',
    ];

    /* -------------------------------------------------------------------------- */
    /* ACCESSEURS                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Accesseur pour le matricule (via StudentProfile)
     * Permet de faire $etudiant->matricule directement
     */
    public function getMatriculeAttribute(): ?string
    {
        return $this->studentProfile?->matricule;
    }

    /**
     * Accesseur pour le nom complet
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /* -------------------------------------------------------------------------- */
    /* RELATIONS STRUCTURELLES                           */
    /* -------------------------------------------------------------------------- */

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
    }

    /* -------------------------------------------------------------------------- */
    /* RELATIONS PÉDAGOGIQUES                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Notes de CC, EXAMEN, RATTRAPAGE
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Absences de l'étudiant
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /* -------------------------------------------------------------------------- */
    /* RELATIONS DE RÉSULTATS                            */
    /* -------------------------------------------------------------------------- */

    public function resultatsMatieres(): HasMany 
    { 
        return $this->hasMany(ResultatMatiere::class); 
    }

    public function resultatsUes(): HasMany 
    { 
        return $this->hasMany(ResultatUe::class); 
    }

    public function resultatsSemestres(): HasMany
    {
        return $this->hasMany(ResultatSemestre::class);
    }

    /**
     * Relation appelée par le Dashboard (Jury/Délibérations)
     */
    public function resultatsAnnuel(): HasMany
    {
        return $this->hasMany(ResultatAnnuel::class);
    }
}