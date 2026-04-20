<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    protected $fillable = [
        'libelle',
        'classe_id'
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function ues()
    {
        return $this->hasMany(Ue::class);
    }

    public function resultatsSemestres()
    {
        return $this->hasMany(ResultatSemestre::class);
    }
}