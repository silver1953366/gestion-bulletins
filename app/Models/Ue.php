<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ue extends Model
{
    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'ues';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'libelle',
        'semestre_id',
        'coefficient',
        'credits'
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'coefficient' => 'integer',
        'credits' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * --- RELATIONS ---
     */

    /**
     * Obtient le semestre auquel appartient l'UE.
     */
    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    /**
     * Obtient les matières rattachées à cette Unité d'Enseignement.
     */
    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class);
    }

    /**
     * Obtient les résultats calculés pour cette UE.
     */
    public function resultatsUes(): HasMany
    {
        return $this->hasMany(ResultatUe::class);
    }

    /**
     * --- ACCESSEURS (LOGIQUE MÉTIER) ---
     */

    /**
     * Calcule dynamiquement le total des coefficients des matières.
     * Permet de vérifier si la somme des matières correspond au coeff de l'UE.
     */
    public function getTotalWeightAttribute(): int
    {
        return $this->matieres()->sum('coefficient');
    }

    /**
     * Formate l'affichage du code et du libellé.
     */
    public function getFullLabelAttribute(): string
    {
        return "[$this->code] $this->libelle";
    }
}