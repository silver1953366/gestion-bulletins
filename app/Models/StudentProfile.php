<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $table = 'student_profiles';

    protected $fillable = [
        'user_id',
        'etudiant_id',
        'matricule'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}