<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $fillable = [
        'cle',
        'valeur',
        'description'
    ];

    protected $casts = [
        'valeur' => 'array'
    ];

    public static function getValue($key, $default = null)
    {
        $param = static::where('cle', $key)->first();
        return $param ? $param->valeur : $default;
    }

    public static function setValue($key, $value, $description = null)
    {
        return static::updateOrCreate(
            ['cle' => $key],
            ['valeur' => $value, 'description' => $description]
        );
    }
}