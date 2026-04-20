<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatUe extends Model
{
    protected $table = 'resultats_ues';

    protected $fillable = [
        'etudiant_id',
        'ue_id',
        'moyenne',
        'credits_acquis',
        'compense'
    ];

    protected $casts = [
        'moyenne' => 'decimal:2',
        'credits_acquis' => 'integer',
        'compense' => 'boolean'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function ue()
    {
        return $this->belongsTo(Ue::class);
    }

    public function isValide()
    {
        return $this->moyenne >= 10;
    }
}