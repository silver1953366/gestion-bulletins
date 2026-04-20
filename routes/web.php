<?php

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
use App\Http\Controllers\Admin\AnneeAcademiqueController;
use App\Http\Controllers\Admin\AbsenceController;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
*/

// ---------------------------
// 1. PAGE D'ACCUEIL
// ---------------------------
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');


// ---------------------------
// 2. AUTHENTIFICATION (Breeze)
// ---------------------------
require __DIR__.'/auth.php';


// ---------------------------
// 3. REDIRECTION DYNAMIQUE (Le Cerveau)
// ---------------------------
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if (!$user->role) {
        return redirect()->route('welcome')
            ->withErrors(['role' => 'Rôle non défini pour cet utilisateur.']);
    }

    $roleName = strtolower($user->role->nom);

    return match ($roleName) {
        'admin'      => redirect()->route('admin.dashboard'),
        'enseignant' => redirect()->route('enseignant.dashboard'),
        'secretaire' => redirect()->route('secretaire.dashboard'),
        'etudiant'   => redirect()->route('etudiant.dashboard'),
        default      => redirect()->route('welcome'),
    };
})->name('dashboard');


// ---------------------------
// 4. ROUTES PROFIL (Authentifiés)
// ---------------------------
Route::middleware('auth')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/update', [ProfileController::class, 'updateProfile'])->name('profile.updateProfile');
    Route::post('/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::post('/request-password-code', [ProfileController::class, 'requestPasswordCode'])->name('profile.requestPasswordCode');
    Route::get('/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    
    Route::post('/request-current-email-code', [ProfileController::class, 'requestCurrentEmailCode'])->name('profile.requestCurrentEmailCode');
    Route::post('/verify-current-email', [ProfileController::class, 'verifyCurrentEmail'])->name('profile.verifyCurrentEmail');
    Route::post('/request-new-email-code', [ProfileController::class, 'requestNewEmailCode'])->name('profile.requestNewEmailCode');
    Route::post('/update-email', [ProfileController::class, 'updateEmail'])->name('profile.updateEmail');
    Route::post('/cancel-email-change', [ProfileController::class, 'cancelEmailChange'])->name('profile.cancelEmailChange');
    Route::post('/clear-sessions', [ProfileController::class, 'clearAllSessions'])->name('profile.clearAllSessions');
});// ---------------------------


// Routes ADMIN
// ---------------------------
 Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Étudiants sans notes
    Route::get('/etudiants/sans-notes', function () {
        return "Liste des étudiants sans notes";
    })->name('etudiants.sans-notes');

    // Matières sans enseignant
    Route::get('/matieres/sans-enseignant', function () {
        return "Matières sans enseant";
    })->name('matieres.sans-enseignant');

    // Absences excessives
    Route::get('/absences/excessives', function () {
        return "Étudiants avec absences excessives";
    })->name('absences.excessives');

    // Incohérences
    Route::get('/incoherences', function () {
        return "Incohérences détectées";
    })->name('incohérences');

    // Années académiques
    Route::get('/annees-academiques', function () {
        return "Gestion des années académiques";
    })->name('annees-academiques');



    Route::resource('admin/etudiants', EtudiantController::class);
Route::resource('admin/matieres', MatiereController::class);
Route::resource('admin/ues', UeController::class);
Route::resource('admin/absences', AbsenceController::class);
Route::resource('admin/bulletins', BulletinController::class);
Route::resource('admin/users', UserController::class);
});

//     Route::resource('etudiants', EtudiantController::class);
//     Route::resource('ues', UEController::class);
//     Route::resource('matieres', MatiereController::class);
//     Route::resource('users', UtilisateurController::class);
//     Route::resource('semestres', SemestreController::class);

//     Route::get('/notes', [NoteController::class, 'index'])->name('admin.notes.index');
//     Route::post('/notes/recalcul', [NoteController::class, 'recalculGlobal'])->name('admin.notes.recalcul');
//     Route::get('/bulletins', [BulletinController::class, 'index'])->name('admin.bulletins.index');
//     Route::get('/jury', [JuryController::class, 'index'])->name('admin.jury.index');
//     Route::post('/jury/valider', [JuryController::class, 'valider'])->name('admin.jury.valider');
//     Route::get('/parametres', [ParametreController::class, 'index'])->name('admin.parametres.index');

//     Route::get('/bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
//     Route::get('/bulletins/{id}/pdf', [BulletinController::class, 'pdf'])->name('bulletins.pdf');

//     Route::get('/jury', [JuryController::class, 'index'])->name('jury.index');
//     Route::post('/jury/calcul', [JuryController::class, 'calculGlobal'])->name('jury.calcul');
//     Route::post('/jury/{id}', [JuryController::class, 'update'])->name('jury.update');

//     Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
//     Route::post('/notes/store', [NoteController::class, 'store'])->name('notes.store');
//     Route::post('/notes/recalcul-global', [NoteController::class, 'recalculGlobal'])->name('notes.recalculGlobal');
//     Route::post('/notes/set-rattrapage-type', [NoteController::class, 'setRattrapageType'])->name('notes.setRattrapageType');

 //});

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


// ---------------------------
// 5. ROUTES ADMIN (Protégées)
// ---------------------------
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- ROUTES CORRIGÉES (Requises par les alertes du DashboardController) ---
    
    // Alertes Étudiants & Scolarité
    Route::get('/etudiants/sans-notes', [EtudiantController::class, 'sansNotes'])->name('etudiants.sans-notes');
    Route::get('/matieres/sans-enseignant', [MatiereController::class, 'sansEnseignant'])->name('matieres.sans-enseignant');
    Route::get('/absences/excessives', [AbsenceController::class, 'excessives'])->name('absences.excessives');
    Route::get('/incoherences', [DashboardController::class, 'incoherences'])->name('incohérences');
    Route::get('/annees-academiques', [AnneeAcademiqueController::class, 'index'])->name('annees-academiques');

    // --- RESSOURCES ---
    Route::resource('etudiants', EtudiantController::class);
    Route::resource('ues', UEController::class);
    Route::resource('matieres', MatiereController::class);
    Route::resource('users', UtilisateurController::class);
    Route::resource('semestres', SemestreController::class);
    Route::resource('notes', NoteController::class);
    
    // Bulletins & Jury
    Route::get('/bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
    Route::get('/jury', [DashboardController::class, 'juryIndex'])->name('jury.index');
});


// ---------------------------
// 6. DÉCONNEXION & SÉCURITÉ
// ---------------------------
// On utilise le contrôleur de Breeze pour la déconnexion
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');