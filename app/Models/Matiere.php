<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Matiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'coefficient',
        'credits',
        'ue_id'
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'credits' => 'integer',
        'ue_id' => 'integer'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function ue(): BelongsTo
    {
        return $this->belongsTo(Ue::class, 'ue_id');
    }

    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(TeacherProfile::class, 'enseignant_matiere', 'matiere_id', 'teacher_profile_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function resultatsMatieres(): HasMany
    {
        return $this->hasMany(ResultatMatiere::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATEURS
    |--------------------------------------------------------------------------
    */

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }
}