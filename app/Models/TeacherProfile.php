<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $table = 'teacher_profiles';

    protected $fillable = [
        'user_id',
        'specialite',
        'grade'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enseignantMatieres()
    {
        return $this->hasMany(EnseignantMatiere::class);
    }

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'enseignant_matiere', 'teacher_profile_id', 'matiere_id');
    }
}