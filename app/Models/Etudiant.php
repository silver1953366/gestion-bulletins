<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Etudiant extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'bac',
        'provenance',
        'photo', // Conservé car présent en base, mais l'accesseur donnera la priorité à User
        'is_finalized'
    ];

    /**
     * Le transtypage des attributs.
     */
    protected $casts = [
        'date_naissance' => 'date:Y-m-d',
        'is_finalized'   => 'boolean',
    ];

    /* -------------------------------------------------------------------------- */
    /* ACCESSEURS (Logique métier)                                               */
    /* -------------------------------------------------------------------------- */

    /**
     * Récupère la photo depuis le compte User lié.
     * Si l'utilisateur n'a pas de photo ou n'existe pas, renvoie un avatar par défaut.
     * Appel dans Blade : {{ $etudiant->profile_photo }}
     */
    public function getProfilePhotoAttribute(): string
    {
        // On tente de récupérer le chemin depuis l'User lié au StudentProfile
        $userPhoto = $this->studentProfile?->user?->photo;

        if ($userPhoto && Storage::disk('public')->exists($userPhoto)) {
            return asset('storage/' . $userPhoto);
        }

        // Retourne une image par défaut si aucune photo n'est trouvée
        return asset('images/default-avatar.png'); 
    }

    /**
     * Accesseur pour le matricule (via StudentProfile)
     * Appel dans Blade : {{ $etudiant->matricule }}
     */
    public function getMatriculeAttribute(): ?string
    {
        return $this->studentProfile?->matricule;
    }

    /**
     * Accesseur pour le nom complet
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /* -------------------------------------------------------------------------- */
    /* RELATIONS                                                                  */
    /* -------------------------------------------------------------------------- */

    /**
     * Lien vers le profil académique (Pivot entre User et Etudiant)
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class, 'etudiant_id');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function bulletins(): HasMany
    {
        return $this->hasMany(Bulletin::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function resultatsMatieres(): HasMany 
    { 
        return $this->hasMany(ResultatMatiere::class); 
    }

    public function resultatsUes(): HasMany 
    { 
        return $this->hasMany(ResultatUe::class); 
    }

    public function resultatsSemestres(): HasMany
    {
        return $this->hasMany(ResultatSemestre::class);
    }

    public function resultatsAnnuel(): HasMany
    {
        return $this->hasMany(ResultatAnnuel::class);
    }
}