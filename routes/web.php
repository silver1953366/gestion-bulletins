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
Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// 2. AUTH
require __DIR__.'/auth.php';


// 3. DASHBOARD (ROUTING PAR RÔLE)
Route::get('/dashboard', function () {

    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user()->load('role');

    if (!$user->role) {
        Auth::logout();
        return redirect()->route('login')
            ->with('error', "Rôle introuvable.");
    }

    $roleName = strtolower($user->role->nom);

    return match ($roleName) {
        'admin' => redirect()->route('admin.dashboard'),
        'enseignant' => redirect()->route('enseignant.dashboard'),
        's.pedagogique' => redirect()->route('secretariat.dashboard'),
        'etudiant' => redirect()->route('etudiant.dashboard'),
        default => redirect()->route('welcome')
            ->with('error', "Rôle non reconnu."),
    };
})->name('dashboard');


// 4. MODULES
require __DIR__.'/admin.php';
require __DIR__.'/enseignant.php';
// require __DIR__.'/etudiant.php';
require __DIR__.'/secretariat.php';


// 5. LOGOUT
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');