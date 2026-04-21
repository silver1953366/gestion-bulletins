<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// 1. ACCUEIL
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// 2. AUTHENTIFICATION (Laravel Breeze/Jetstream)
require __DIR__.'/auth.php';

// 3. LE CERVEAU (Redirection selon le rôle)
Route::get('/dashboard', function () {
    if (!Auth::check()) return redirect()->route('login');

    $user = Auth::user();
    $roleName = strtolower($user->role->nom ?? '');

    return match ($roleName) {
        'admin'      => redirect()->route('admin.dashboard'),
        'enseignant' => redirect()->route('enseignant.dashboard'),
        'secretaire' => redirect()->route('secretariat.dashboard'),
        'etudiant'   => redirect()->route('etudiant.dashboard'),
        default      => redirect()->route('welcome'),
    };
})->name('dashboard');

// 4. APPEL DES ROUTES MODULAIRES
require __DIR__.'/admin.php';
//require __DIR__.'/enseignant.php';
//require __DIR__.'/etudiant.php';
//require __DIR__.'/secretariat-pedagogique.php';

// 5. DECONNEXION
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');