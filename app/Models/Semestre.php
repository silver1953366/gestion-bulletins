<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semestre extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     * Utilisation de 'classe_id' conformément à votre schéma de BDD.
     */
    protected $fillable = [
        'libelle',
        'annee_universitaire',
        'classe_id',
    ];

    /**
     * RELATION : Un semestre appartient à une classe (la classe de référence).
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    /**
     * RELATION : Un semestre possède plusieurs Unités d'Enseignement (UE).
     */
    public function ues(): HasMany
    {
        return $this->hasMany(Ue::class);
    }

    /**
     * MÉTHODE CORRIGÉE : Logique de Tronc Commun.
     * C'est cette méthode que le modal du fichier Blade appelle.
     * Elle permet de trouver toutes les classes (ex: GI1A, GI1B) liées au même parcours.
     */
    public function getClassesPartageantProgramme()
    {
        // On vérifie d'abord si la relation classe existe pour éviter une erreur sur un objet null
        if (!$this->classe) {
            return collect(); 
        }

        return Classe::where('filiere_id', $this->classe->filiere_id)
                     ->where('niveau_id', $this->classe->niveau_id)
                     ->get();
    }

    /**
     * ACCESSEUR : Pour récupérer le nom de la filière plus facilement.
     * Usage : $semestre->filiere_nom
     */
    public function getFiliereNomAttribute(): string
    {
        return $this->classe->filiere->nom ?? 'Filière non définie';
    }

    /**
     * ACCESSEUR : Pour récupérer le code du niveau.
     * Usage : $semestre->niveau_code
     */
    public function getNiveauCodeAttribute(): string
    {
        return $this->classe->niveau->code ?? 'N/A';
    }

    /**
     * ACCESSEUR : Pour afficher le parcours complet.
     * Usage : $semestre->full_parcours
     */
    public function getFullParcoursAttribute(): string
    {
        return $this->filiere_nom . ' - ' . $this->niveau_code;
    }
}