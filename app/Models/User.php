<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Attributs assignables en masse.
     * Correspond exactement à ta migration (first_name, last_name, etc.)
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'photo',
        'role_id',
    ];

    /**
     * Attributs masqués pour les tableaux/JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversion automatique des types.
     * Le cast 'hashed' sur le password permet de ne plus faire Hash::make() dans le controller.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * --- ACCESSEURS ---
     */

    /**
     * Retourne le nom complet formaté (Minko Marc).
     * Utilisation dans Blade : {{ $user->full_name }}
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}"),
        );
    }

    /**
     * --- RELATIONS ---
     */

    /**
     * Rôle système (Relation : Un utilisateur appartient à un Rôle).
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Profil académique (si l'utilisateur est un étudiant).
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Profil professionnel (si l'utilisateur est un enseignant).
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class, 'user_id');
    }

    /**
     * Évaluations créées (si l'utilisateur est Admin ou Enseignant).
     */
    public function evaluationsCreated(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'created_by');
    }

    /**
     * Absences enregistrées par cet utilisateur.
     */
    public function absencesCreated(): HasMany
    {
        return $this->hasMany(Absence::class, 'created_by');
    }

    /**
     * Historique des imports de notes.
     */
    public function importsNotes(): HasMany
    {
        return $this->hasMany(ImportNote::class, 'created_by');
    }

    /**
     * Logs d'audit.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * --- LOGIQUE MÉTIER / HELPERS ---
     */

    /**
     * Vérifie si l'utilisateur possède un rôle spécifique.
     * @param string $roleNom (ex: 'admin', 'etudiant', 'enseignant')
     */
    public function hasRole(string $roleNom): bool
    {
        // On utilise la méthode facultative ?. de PHP 8 pour éviter les erreurs si role est null
        return strtolower($this->role?->nom ?? '') === strtolower($roleNom);
    }

    /**
     * Raccourci pour vérifier si l'utilisateur est Administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Raccourci pour vérifier si l'utilisateur est Étudiant.
     */
    public function isEtudiant(): bool
    {
        return $this->hasRole('etudiant');
    }

    /**
     * Raccourci pour vérifier si l'utilisateur est Enseignant.
     */
    public function isEnseignant(): bool
    {
        return $this->hasRole('enseignant');
    }
}