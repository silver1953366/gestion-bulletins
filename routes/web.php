<?php

use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EtudiantController;
use App\Http\Controllers\Admin\UEController;
use App\Http\Controllers\Admin\MatiereController;
use App\Http\Controllers\Admin\UtilisateurController;
use App\Http\Controllers\Admin\SemestreController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\BulletinController;
use App\Http\Controllers\Admin\JuryController;
use App\Http\Controllers\Admin\ParametreController;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
*/

// ---------------------------
// Page d'accueil 
// ---------------------------
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
});

// ---------------------------
// Auth Breeze (login, register, etc.)
// ---------------------------
require __DIR__.'/auth.php';

// ---------------------------
// Vérification par code (2FA)
// ---------------------------
Route::get('/verify', [VerificationController::class, 'showVerifyForm'])->name('verify.form');
Route::post('/verify', [VerificationController::class, 'verify'])->name('verify.check');

// ---------------------------
// Redirection Dashboard central
// ---------------------------
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if (!$user->role) {
        return redirect()->route('home')
            ->withErrors(['role' => 'Rôle non défini pour cet utilisateur.']);
    }

    // IMPORTANT : colonne = "nom"
    $roleName = strtolower($user->role->nom);

    return match ($roleName) {
        'admin' => redirect()->route('admin.dashboard'),
        'enseignant' => redirect()->route('enseignant.dashboard'),
        'secretaire' => redirect()->route('secretaire.dashboard'),
        'etudiant' => redirect()->route('etudiant.dashboard'),
        default => redirect()->route('home'),
    };
})->name('dashboard');

// ---------------------------
// Routes PROFIL
// ---------------------------
Route::middleware('auth')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/update', [ProfileController::class, 'updateProfile'])->name('profile.updateProfile');
    Route::post('/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::post('/request-password-code', [ProfileController::class, 'requestPasswordCode'])->name('profile.requestPasswordCode');
    Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/request-current-email-code', [ProfileController::class, 'requestCurrentEmailCode'])->name('profile.requestCurrentEmailCode');
    Route::post('/verify-current-email', [ProfileController::class, 'verifyCurrentEmail'])->name('profile.verifyCurrentEmail');
    Route::post('/request-new-email-code', [ProfileController::class, 'requestNewEmailCode'])->name('profile.requestNewEmailCode');
    Route::post('/update-email', [ProfileController::class, 'updateEmail'])->name('profile.updateEmail');
    Route::post('/cancel-email-change', [ProfileController::class, 'cancelEmailChange'])->name('profile.cancelEmailChange');
    Route::post('/clear-sessions', [ProfileController::class, 'clearAllSessions'])->name('profile.clearAllSessions');
});// ---------------------------


// Routes ADMIN
// ---------------------------
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('etudiants', EtudiantController::class);
    Route::resource('ues', UEController::class);
    Route::resource('matieres', MatiereController::class);
    Route::resource('users', UtilisateurController::class);
    Route::resource('semestres', SemestreController::class);

    Route::get('/notes', [NoteController::class, 'index'])->name('admin.notes.index');
    Route::post('/notes/recalcul', [NoteController::class, 'recalculGlobal'])->name('admin.notes.recalcul');
    Route::get('/bulletins', [BulletinController::class, 'index'])->name('admin.bulletins.index');
    Route::get('/jury', [JuryController::class, 'index'])->name('admin.jury.index');
    Route::post('/jury/valider', [JuryController::class, 'valider'])->name('admin.jury.valider');
    Route::get('/parametres', [ParametreController::class, 'index'])->name('admin.parametres.index');

    Route::get('/bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
    Route::get('/bulletins/{id}/pdf', [BulletinController::class, 'pdf'])->name('bulletins.pdf');

    Route::get('/jury', [JuryController::class, 'index'])->name('jury.index');
    Route::post('/jury/calcul', [JuryController::class, 'calculGlobal'])->name('jury.calcul');
    Route::post('/jury/{id}', [JuryController::class, 'update'])->name('jury.update');

    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes/store', [NoteController::class, 'store'])->name('notes.store');
    Route::post('/notes/recalcul-global', [NoteController::class, 'recalculGlobal'])->name('notes.recalculGlobal');
    Route::post('/notes/set-rattrapage-type', [NoteController::class, 'setRattrapageType'])->name('notes.setRattrapageType');

});

// ---------------------------
// Routes Deconnexion
// ---------------------------
Route::post('/logout', [ProfileController::class, 'logout'])->name('logout');

// ---------------------------
// Routes de secours pour les erreurs 404
// ---------------------------
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
