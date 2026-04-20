<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatAnnuel extends Model
{
    protected $table = 'resultats_annuels';

    protected $fillable = [
        'etudiant_id',
        'annee_academique_id',
        'moyenne',
        'decision',
        'mention'
    ];

    protected $casts = [
        'moyenne' => 'decimal:2'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public function getMentionText()
    {
        $mentions = [
            'PASSABLE' => [10, 12],
            'ASSEZ_BIEN' => [12, 14],
            'BIEN' => [14, 16],
            'TRES_BIEN' => [16, 18],
            'EXCELLENT' => [18, 20]
        ];

        foreach ($mentions as $mention => [$min, $max]) {
            if ($this->moyenne >= $min && $this->moyenne < $max) {
                return $mention;
            }
        }

        return $this->moyenne >= 10 ? 'ADMIS' : 'NON_ADMIS';
    }
}