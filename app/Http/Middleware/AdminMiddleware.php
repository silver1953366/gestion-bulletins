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
        // 1. On vérifie d'abord si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. On récupère le nom du rôle et on le passe en minuscules pour la comparaison
        // Cela évite les erreurs si le rôle est enregistré en tant que "Admin" ou "ADMIN"
        $roleName = Auth::user()->role ? strtolower(Auth::user()->role->nom) : '';

        // 3. Vérification du privilège
        if ($roleName === 'admin') {
            return $next($request);
        }

        // 4. Si l'utilisateur n'est pas admin, redirection vers l'accueil avec un message
        return redirect()->route('welcome')->with('error', "Accès refusé : Vous n'avez pas les privilèges d'administrateur.");
    }
}