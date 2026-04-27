{{-- routes/secretariat.php --}}
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Secretariat\DashboardController;
use App\Http\Controllers\Secretariat\EtudiantController;
use App\Http\Controllers\Secretariat\NoteController;
use App\Http\Controllers\Secretariat\AbsenceController;
use App\Http\Controllers\Secretariat\BulletinController;
use App\Http\Controllers\Secretariat\ResultatController;

Route::prefix('secretariat')
    ->name('secretariat.')
    ->middleware(['auth'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Gestion des étudiants
        Route::resource('etudiants', EtudiantController::class);
        Route::get('/etudiants/{id}/fiche', [EtudiantController::class, 'fiche'])->name('etudiants.fiche');

        // Gestion des notes
        Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
        Route::post('/notes/save', [NoteController::class, 'saveNote'])->name('notes.save');
        Route::post('/notes/batch', [NoteController::class, 'batchSave'])->name('notes.batch');
        Route::get('/notes/export', [NoteController::class, 'export'])->name('notes.export');

        // Gestion des absences
        Route::resource('absences', AbsenceController::class);
        Route::get('/absences/penalites/{etudiant_id}', [AbsenceController::class, 'penalites'])->name('absences.penalites');

        // Bulletins
        Route::get('/bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
        Route::post('/bulletins/generate', [BulletinController::class, 'generate'])->name('bulletins.generate');
        Route::get('/bulletins/download/{id}', [BulletinController::class, 'download'])->name('bulletins.download');
        Route::post('/bulletins/export-pdf', [BulletinController::class, 'exportPdf'])->name('bulletins.export-pdf');

        // Suivi des résultats
        Route::get('/resultats', [ResultatController::class, 'index'])->name('resultats.index');
        Route::get('/resultats/moyennes', [ResultatController::class, 'moyennes'])->name('resultats.moyennes');
        Route::get('/resultats/credits', [ResultatController::class, 'credits'])->name('resultats.credits');
        Route::get('/resultats/jury', [ResultatController::class, 'jury'])->name('resultats.jury');
        Route::post('/resultats/validate', [ResultatController::class, 'validateJury'])->name('resultats.validate');
    });