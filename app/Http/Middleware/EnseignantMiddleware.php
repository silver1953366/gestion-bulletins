<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnseignantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier connexion
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Récupérer rôle
        $roleName = Auth::user()->role ? strtolower(Auth::user()->role->nom) : '';

        // Autoriser uniquement enseignant
        if ($roleName === 'enseignant') {
            return $next($request);
        }

        // Refuser accès
        return redirect()->route('welcome')
            ->with('error', "Accès refusé : réservé aux enseignants.");
    }
}