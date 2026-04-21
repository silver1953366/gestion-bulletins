<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Gère une requête entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. On vérifie si l'utilisateur est authentifié
        // 2. On vérifie s'il a un rôle et si ce rôle est 'admin'
        if (Auth::check() && Auth::user()->role && Auth::user()->role->nom === 'admin') {
            return $next($request);
        }

        // Si l'utilisateur n'est pas admin, on le redirige vers l'accueil
        // avec un message d'alerte (Flash message)
        return redirect()->route('welcome')->with('error', "Accès refusé : Vous n'avez pas les privilèges d'administrateur.");
    }
}