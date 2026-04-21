<?php

use Illuminate\Support\Facades\Route;

// Imports des Contrôleurs
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EtudiantController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\Admin\ClasseController;
use App\Http\Controllers\Admin\SemestreController;
use App\Http\Controllers\Admin\DepartementController;
use App\Http\Controllers\Admin\FiliereController;
use App\Http\Controllers\Admin\UeController;
use App\Http\Controllers\Admin\MatiereController;
use App\Http\Controllers\Admin\EnseignantMatiereController;
use App\Http\Controllers\Admin\AnneeAcademiqueController;
use App\Http\Controllers\Admin\AbsenceController;
use App\Http\Controllers\Admin\BulletinController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\Admin\ResultatMatiereController;
use App\Http\Controllers\Admin\ResultatUeController;
use App\Http\Controllers\Admin\ResultatSemestreController;
use App\Http\Controllers\Admin\ResultatAnnuelController;
use App\Http\Controllers\ImportNoteController;

/*
|--------------------------------------------------------------------------
| Routes d'Administration - INPTIC
|--------------------------------------------------------------------------
| Ce fichier centralise la gestion académique globale (LP ASUR / DAR).
| Le middleware 'admin' garantit un accès restreint.
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // 0. DASHBOARD (Le Cerveau Statistique)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. GESTION DES UTILISATEURS & IDENTITÉS NUMÉRIQUES
    Route::resource('users', UserController::class);
    Route::resource('etudiants', EtudiantController::class);
    Route::resource('student-profiles', StudentProfileController::class)->except(['create', 'show', 'edit']);

    // 2. STRUCTURES ACADÉMIQUES (Hiérarchie Institutionnelle)
    Route::resource('departements', DepartementController::class);
    Route::resource('filieres', FiliereController::class);
    Route::resource('classes', ClasseController::class);
    Route::resource('semestres', SemestreController::class);

    // 3. PROGRAMME PÉDAGOGIQUE (Architecture LMD)
    Route::resource('ues', UeController::class);
    Route::resource('matieres', MatiereController::class);

    // 4. CORPS ENSEIGNANT & ATTRIBUTIONS
    Route::resource('teachers', TeacherProfileController::class);
    Route::resource('enseignant-matiere', EnseignantMatiereController::class)->only([
        'index', 'store', 'destroy'
    ]);

    // 5. SYSTÈME DE NOTES (Saisie & Imports Excel)
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/saisie', [EvaluationController::class, 'formulaireSaisie'])->name('saisie');
        Route::post('/store', [EvaluationController::class, 'store'])->name('store');
    });

    Route::prefix('imports')->name('imports.')->group(function () {
        Route::get('/', [ImportNoteController::class, 'index'])->name('index');
        Route::post('/store', [ImportNoteController::class, 'store'])->name('store');
        Route::delete('/{importNote}', [ImportNoteController::class, 'destroy'])->name('destroy');
    });

    // 6. MOTEUR DE CALCULS ACADÉMIQUES (Logique de délibération)
    Route::prefix('resultats')->name('resultats.')->group(function () {
        
        // A. MATIÈRES : Moyennes pondérées et impact des absences
        Route::prefix('matieres')->name('matieres.')->group(function () {
            Route::get('/', [ResultatMatiereController::class, 'index'])->name('index');
            Route::post('/calculer', [ResultatMatiereController::class, 'calculerPourClasse'])->name('calculer');
        });

        // B. UNITES D'ENSEIGNEMENT (UE) : Validation des crédits (ECTS)
        Route::prefix('ues')->name('ues.')->group(function () {
            Route::get('/', [ResultatUeController::class, 'index'])->name('index');
            Route::post('/calculer', [ResultatUeController::class, 'calculerClasse'])->name('calculer-classe');
        });

        // C. SEMESTRES : PV de délibération semestriels
        Route::prefix('semestres')->name('semestres.')->group(function () {
            Route::get('/', [ResultatSemestreController::class, 'index'])->name('index');
            Route::post('/calculer', [ResultatSemestreController::class, 'calculerClasse'])->name('calculer');
        });
    });

    // 7. SUIVI ACADÉMIQUE ET DISCIPLINE
    Route::resource('annees', AnneeAcademiqueController::class);
    Route::resource('absences', AbsenceController::class);

    // 8. JURY ET DÉLIBÉRATIONS ANNUELLES (Grand Jury)
    Route::prefix('jury')->name('jury.')->group(function () {
        Route::get('/annuel', [ResultatAnnuelController::class, 'index'])->name('annuel.index');
        Route::post('/calculer-promo', [ResultatAnnuelController::class, 'calculerPromo'])->name('calculer-promo');
    });

    // 9. GESTION DES BULLETINS (Édition PDF et Archivage)
    Route::prefix('bulletins')->name('bulletins.')->group(function () {
        Route::get('/', [BulletinController::class, 'index'])->name('index');
        Route::get('/{bulletin}/download', [BulletinController::class, 'download'])->name('download');
        Route::delete('/{bulletin}', [BulletinController::class, 'destroy'])->name('destroy');
        Route::post('/generate', [BulletinController::class, 'generate'])->name('generate');
    });

    // 10. CONFIGURATION SYSTÈME
    Route::resource('parametres', ParametreController::class)->only([
        'index', 'store', 'destroy'
    ]);

});