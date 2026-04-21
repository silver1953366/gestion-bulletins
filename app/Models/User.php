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
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
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
     * Conversion des types de colonnes.
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
     * Retourne le nom complet formaté.
     * Utilisation : $user->full_name
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
     * Rôle système de l'utilisateur (Admin, Enseignant, Etudiant).
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Profil académique si l'utilisateur est un étudiant.
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Profil professionnel si l'utilisateur est un enseignant.
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    /**
     * Évaluations créées par cet utilisateur (si Admin/Enseignant).
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
     * Historique des imports de notes effectués.
     */
    public function importsNotes(): HasMany
    {
        return $this->hasMany(ImportNote::class, 'created_by');
    }

    /**
     * Logs d'audit liés aux actions de l'utilisateur.
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
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Vérifie si l'utilisateur est Administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}