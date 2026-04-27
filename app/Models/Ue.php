<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * CLASS Ue
 * * Représente une Unité d'Enseignement (UE).
 * Ce modèle centralise les matières (ECUE) et est rattaché à un semestre.
 *
 * @property int $id
 * @property string $code
 * @property string $libelle
 * @property int $semestre_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Ue extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'ues';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'libelle',
        'semestre_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'semestre_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Obtient le semestre associé (permet de remonter à la Filière/Niveau).
     */
    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }

    /**
     * Obtient la liste des matières (ECUE) rattachées.
     */
    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class, 'ue_id');
    }

    /**
     * Obtient les résultats des étudiants rattachés.
     */
    public function resultatsUes(): HasMany
    {
        return $this->hasMany(ResultatUe::class, 'ue_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSEURS (Calculs dynamiques pour l'interface)
    |--------------------------------------------------------------------------
    */

    /**
     * ACCESSEUR : Somme des coefficients des matières liées.
     * Appelé via $ue->total_matieres_coeff
     */
    public function getTotalMatieresCoeffAttribute(): int
    {
        return (int) ($this->matieres()->sum('coefficient') ?? 0);
    }

    /**
     * ACCESSEUR : Somme des crédits des matières liées.
     * Appelé via $ue->total_matieres_credits
     */
    public function getTotalMatieresCreditsAttribute(): int
    {
        return (int) ($this->matieres()->sum('credits') ?? 0);
    }

    /**
     * ACCESSEUR : Libellé formaté [CODE] Nom.
     */
    public function getFullLabelAttribute(): string
    {
        return "[" . strtoupper($this->code) . "] " . $this->libelle;
    }

    /**
     * ACCESSEUR : Parcours complet (Filière — Niveau — Semestre).
     */
    public function getParcoursCompletAttribute(): string
    {
        if (!$this->semestre || !$this->semestre->classe) {
            return "Parcours non défini";
        }

        $filiere = $this->semestre->classe->filiere->nom ?? 'Filière Inconnue';
        $niveau = $this->semestre->classe->niveau->code ?? 'Niveau Inconnu';
        $semestre = $this->semestre->libelle;

        return "{$filiere} — {$niveau} — {$semestre}";
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATEURS
    |--------------------------------------------------------------------------
    */

    /**
     * Nettoie et met en majuscule le code UE automatiquement.
     */
    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES & LOGIQUE MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Filtre les UE par semestre.
     */
    public function scopeForSemestre(Builder $query, int $semestreId): Builder
    {
        return $query->where('semestre_id', $semestreId);
    }

    /**
     * Vérifie si l'UE peut être supprimée sans risque d'erreur d'intégrité.
     */
    public function canBeDeleted(): bool
    {
        return $this->matieres()->count() === 0 && $this->resultatsUes()->count() === 0;
    }

    /**
     * Duplique l'UE vers un autre semestre (utile pour les nouveaux programmes).
     */
    public function duplicateTo(int $targetSemestreId): self
    {
        $newUe = $this->replicate();
        $newUe->semestre_id = $targetSemestreId;
        $newUe->save();

        return $newUe;
    }
}