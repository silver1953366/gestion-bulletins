<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Parametre extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'cle',
        'valeur',
        'description'
    ];

    /**
     * Les attributs qui doivent être castés.
     * On utilise 'json' pour permettre le stockage de nombres, chaînes ou tableaux.
     *
     * @var array
     */
    protected $casts = [
        'valeur' => 'json'
    ];

    /**
     * Récupère la valeur d'un paramètre par sa clé.
     * * @param string $key La clé unique du paramètre (ex: 'ABSENCE_QUOTA')
     * @param mixed $default Valeur de retour si la clé n'existe pas
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        // Optionnel : Ajout d'une mise en cache pour booster les performances de l'ERP
        return Cache::remember("param_{$key}", 3600, function () use ($key, $default) {
            $param = static::where('cle', $key)->first();
            return $param ? $param->valeur : $default;
        });
    }

    /**
     * Définit ou met à jour un paramètre système.
     * * @param string $key
     * @param mixed $value
     * @param string|null $description
     * @return \App\Models\Parametre
     */
    public static function setValue($key, $value, $description = null)
    {
        $param = static::updateOrCreate(
            ['cle' => $key],
            [
                'valeur' => $value,
                'description' => $description
            ]
        );

        // On vide le cache pour que la nouvelle valeur soit prise en compte immédiatement
        Cache::forget("param_{$key}");

        return $param;
    }

    /**
     * Vérifie si un paramètre existe.
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return static::where('cle', $key)->exists();
    }
}