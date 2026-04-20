<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatMatiere extends Model
{
    protected $table = 'resultats_matieres';

    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'moyenne',
        'utilise_rattrapage'
    ];

    protected $casts = [
        'moyenne' => 'decimal:2',
        'utilise_rattrapage' => 'boolean'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function isValide()
    {
        return $this->moyenne >= 10;
    }
}