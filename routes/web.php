<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes - INPTIC
|--------------------------------------------------------------------------
*/

// 1. ACCUEIL
// Note : On retire la redirection auto vers dashboard ici pour casser la boucle ERR_TOO_MANY_REDIRECTS
Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// 2. AUTHENTIFICATION (Laravel Breeze / auth.php)
require __DIR__.'/auth.php';


// 3. LE CERVEAU (Redirection selon le rôle)
// Cette route sert de point d'entrée unique après le login
Route::get('/dashboard', function () {
    // Si non connecté, direction login
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    // Vérification de l'existence du rôle pour éviter les erreurs null
    if (!$user->role) {
        // Optionnel : déconnexion automatique si aucun rôle n'est trouvé
        Auth::logout();
        return redirect()->route('login')->with('error', "Votre compte n'a aucun rôle assigné. Contactez l'administrateur.");
    }

    // Normalisation du nom du rôle en minuscules
    $roleName = strtolower($user->role->nom);

    // Redirection dynamique
    return match ($roleName) {
        'admin'      => redirect()->route('admin.dashboard'),
        'enseignant' => redirect()->route('enseignant.dashboard'),
        'secretaire' => redirect()->route('secretariat.dashboard'),
        'etudiant'   => redirect()->route('etudiant.dashboard'),
        default      => redirect()->route('welcome')->with('error', "Rôle non reconnu."),
    };
})->name('dashboard');


// 4. APPEL DES ROUTES MODULAIRES
// On charge les fichiers de routes spécifiques
require __DIR__.'/admin.php';

// Décommenter au fur et à mesure de la création des fichiers
// require __DIR__.'/enseignant.php';
// require __DIR__.'/etudiant.php';
// require __DIR__.'/secretariat.php';


// 5. DECONNEXION
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');