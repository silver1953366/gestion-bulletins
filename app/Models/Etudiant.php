<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'bac',
        'provenance'
    ];

    protected $casts = [
        'date_naissance' => 'date'
    ];

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function resultatsMatieres()
    {
        return $this->hasMany(ResultatMatiere::class);
    }

    public function resultatsUes()
    {
        return $this->hasMany(ResultatUe::class);
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

    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getCurrentInscription()
    {
        return $this->inscriptions()
            ->whereHas('anneeAcademique', function($query) {
                $query->where('active', true);
            })
            ->with(['classe.filiere.departement', 'classe.niveau'])
            ->first();
    }
}