<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Routes protégées (authentification requise)
Route::middleware('auth')->group(function () {

    // DASHBOARD : affiche la liste des utilisateurs
    Route::get('/dashboard', [UserController::class, 'index'])
        ->name('dashboard')->middleware('auth');

    // Actions utilisateur depuis le dashboard
    Route::post('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // Page principale -> tableaux projets
    Route::get('/', [ProjectController::class, 'index'])->name('project.index');

    // Projets
    Route::post('/project', [ProjectController::class, 'store'])->name('project.store');
    Route::post('/project/save/{project}', [ProjectController::class, 'save'])->name('project.save');
    Route::delete('/project/{project}', [ProjectController::class, 'destroy'])->name('project.destroy');

    // Profil Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
    ->name('users.destroy');



});

// Auth (login, register, logout…)
require __DIR__.'/auth.php';
