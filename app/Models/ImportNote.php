<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportNote extends Model
{
    protected $table = 'imports_notes';

    protected $fillable = [
        'fichier',
        'statut',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}