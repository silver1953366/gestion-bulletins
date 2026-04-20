<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $fillable = [
        'code',
        'libelle',
        'coefficient',
        'credits',
        'ue_id'
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'credits' => 'integer'
    ];

    public function ue()
    {
        return $this->belongsTo(Ue::class);
    }

    public function enseignantMatieres()
    {
        return $this->hasMany(EnseignantMatiere::class);
    }

    public function enseignants()
    {
        return $this->belongsToMany(TeacherProfile::class, 'enseignant_matiere', 'matiere_id', 'teacher_profile_id');
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
}