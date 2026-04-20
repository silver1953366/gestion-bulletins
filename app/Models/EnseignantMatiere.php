<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnseignantMatiere extends Model
{
    protected $table = 'enseignant_matiere';

    protected $fillable = [
        'teacher_profile_id',
        'matiere_id'
    ];

    public function teacherProfile()
    {
        return $this->belongsTo(TeacherProfile::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
}