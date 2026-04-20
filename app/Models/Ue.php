<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ue extends Model
{
    protected $table = 'ues';

    protected $fillable = [
        'code',
        'libelle',
        'semestre_id',
        'coefficient',
        'credits'
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'credits' => 'integer'
    ];

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    public function matieres()
    {
        return $this->hasMany(Matiere::class);
    }

    public function resultatsUes()
    {
        return $this->hasMany(ResultatUe::class);
    }
}