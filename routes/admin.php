<?php

use Illuminate\Support\Facades\Route;

/**
 * Imports des Contrôleurs du Namespace Admin
 */
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EtudiantController;
use App\Http\Controllers\Admin\StudentProfileController;
use App\Http\Controllers\Admin\TeacherProfileController;
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
use App\Http\Controllers\Admin\ImportNoteController;
use App\Http\Controllers\Admin\AuditLogController;

/*
|--------------------------------------------------------------------------
| Routes d'Administration - INPTIC
|--------------------------------------------------------------------------
| Toutes les routes ci-dessous sont protégées par le middleware 'admin'
| et préfixées par '/admin'. Elles utilisent le nommage 'admin.*'.
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ---------------------------------------------------------
    // 0. TABLEAU DE BORD
    // ---------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // ---------------------------------------------------------
    // 1. GESTION DES UTILISATEURS & IDENTITÉS
    // ---------------------------------------------------------
    // Gestion des comptes de connexion (Login/Email/Rôles)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    
    // Gestion des dossiers administratifs étudiants (Matricule, Niveau...)
    Route::resource('etudiants', EtudiantController::class)->except(['create', 'show', 'edit']);
    
    // Profils détaillés (si séparés de la ressource étudiants)
    Route::resource('student-profiles', StudentProfileController::class)->except(['create', 'show', 'edit']);


    // ---------------------------------------------------------
    // 2. STRUCTURES ACADÉMIQUES
    // ---------------------------------------------------------
    Route::resource('departements', DepartementController::class)->except(['create', 'show', 'edit']);
    Route::resource('filieres', FiliereController::class)->except(['create', 'show', 'edit']);
    Route::resource('classes', ClasseController::class)->except(['create', 'show', 'edit']);
    Route::resource('semestres', SemestreController::class)->except(['create', 'show', 'edit']);


    // ---------------------------------------------------------
    // 3. PROGRAMME PÉDAGOGIQUE (LMD)
    // ---------------------------------------------------------
    // Unités d'Enseignement (ex: UE Fondamentale)
    Route::resource('ues', UeController::class)->except(['create', 'show', 'edit']);
    
    // Matières rattachées aux UEs avec leurs crédits et coefficients
    Route::resource('matieres', MatiereController::class)->except(['create', 'show', 'edit']);


    // ---------------------------------------------------------
    // 4. CORPS ENSEIGNANT & ATTRIBUTIONS
    // ---------------------------------------------------------
    // Profils des enseignants
    Route::resource('teachers', TeacherProfileController::class)->except(['create', 'show', 'edit']);
    
    // Association des enseignants aux matières (Attributions)
    Route::resource('enseignant-matiere', EnseignantMatiereController::class)->only(['index', 'store', 'destroy']);


    // ---------------------------------------------------------
    // 5. SYSTÈME DE NOTES (Saisie & Imports)
    // ---------------------------------------------------------
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        // Choix de la matière et de la classe
        Route::get('/', [EvaluationController::class, 'index'])->name('index'); 
        // Formulaire de saisie dynamique
        Route::get('/saisie', [EvaluationController::class, 'formulaireSaisie'])->name('saisie');
        // Sauvegarde des notes
        Route::post('/store', [EvaluationController::class, 'store'])->name('store');
    });

    // Importation massive via fichiers Excel
    Route::prefix('imports')->name('imports.')->group(function () {
        Route::get('/', [ImportNoteController::class, 'index'])->name('index');
        Route::post('/store', [ImportNoteController::class, 'store'])->name('store');
        Route::delete('/{importNote}', [ImportNoteController::class, 'destroy'])->name('destroy');
    });


    // ---------------------------------------------------------
    // 6. MOTEUR DE CALCULS ACADÉMIQUES
    // ---------------------------------------------------------
    Route::prefix('resultats')->name('resultats.')->group(function () {
        
        // Calcul des moyennes par matière (CC + Examen)
        Route::prefix('matieres')->name('matieres.')->group(function () {
            Route::get('/', [ResultatMatiereController::class, 'index'])->name('index');
            Route::post('/calculer', [ResultatMatiereController::class, 'calculerPourClasse'])->name('calculer');
        });

        // Calcul des moyennes par Unité d'Enseignement (UE)
        Route::prefix('ues')->name('ues.')->group(function () {
            Route::get('/', [ResultatUeController::class, 'index'])->name('index');
            Route::post('/calculer', [ResultatUeController::class, 'calculerClasse'])->name('calculer-classe');
        });

        // Calcul des moyennes Semestrielles (S1/S2 ou S5/S6)
        Route::prefix('semestres')->name('semestres.')->group(function () {
            Route::get('/', [ResultatSemestreController::class, 'index'])->name('index');
            Route::post('/calculer', [ResultatSemestreController::class, 'calculerClasse'])->name('calculer');
        });
    });


    // ---------------------------------------------------------
    // 7. SUIVI, DISCIPLINE & TEMPS
    // ---------------------------------------------------------
    // Définition des années (ex: 2024-2025)
    Route::resource('annees', AnneeAcademiqueController::class)->except(['create', 'show', 'edit']);
    
    // Gestion des absences étudiants
    Route::resource('absences', AbsenceController::class)->except(['create', 'show', 'edit']);


    // ---------------------------------------------------------
    // 8. JURY ET DÉLIBÉRATIONS (Passage d'année)
    // ---------------------------------------------------------
    Route::prefix('jury')->name('jury.')->group(function () {
        Route::get('/annuel', [ResultatAnnuelController::class, 'index'])->name('annuel.index');
        Route::post('/calculer-promo', [ResultatAnnuelController::class, 'calculerPromo'])->name('calculer-promo');
    });


    // ---------------------------------------------------------
    // 9. BULLETINS (Génération & Impression)
    // ---------------------------------------------------------
    Route::prefix('bulletins')->name('bulletins.')->group(function () {
        Route::get('/', [BulletinController::class, 'index'])->name('index');
        Route::get('/{bulletin}/download', [BulletinController::class, 'download'])->name('download');
        Route::delete('/{bulletin}', [BulletinController::class, 'destroy'])->name('destroy');
        Route::post('/generate', [BulletinController::class, 'generate'])->name('generate');
    });


    // ---------------------------------------------------------
    // 10. SÉCURITÉ ET AUDIT (Logs système)
    // ---------------------------------------------------------
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        // On garde 'show' ici pour consulter le détail d'une action suspecte
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
    });


    // ---------------------------------------------------------
    // 11. CONFIGURATION SYSTÈME
    // ---------------------------------------------------------
    // Paramètres généraux (Logo, signature, noms des responsables)
    Route::resource('parametres', ParametreController::class)->only(['index', 'store', 'destroy']);

});