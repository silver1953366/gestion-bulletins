<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Enseignant\DashboardController;

Route::prefix('enseignant')
    ->name('enseignant.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/notes/save', [DashboardController::class, 'saveNote'])
            ->name('notes.save');

        Route::get('/notes/export', [DashboardController::class, 'exportNotes'])
            ->name('notes.export');

        Route::get('/statistiques', [DashboardController::class, 'statistiques'])
            ->name('statistiques');
    });