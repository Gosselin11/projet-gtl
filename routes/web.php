<?php

use App\Models\Project;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Task;
use App\Models\TaskTemplate;
use Illuminate\Http\Request;
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

    Route::post('/project/{project}/add-table', [ProjectController::class, 'addTable'])->name('table.add');
Route::get('/table/{table}/add-column/{type}', [ProjectController::class, 'addColumn'])->name('column.add');
Route::get('/table/{table}/add-row', [ProjectController::class, 'addRow'])->name('row.add');
Route::delete('/table/{table}', [ProjectController::class, 'destroyTable'])->name('table.destroy');
Route::post('/project/{project}/add-fixed', [ProjectController::class, 'addFixedTable'])->name('table.addFixed');
Route::post('/project/{project}/add-custom', [ProjectController::class, 'addCustomTable'])->name('table.addCustom');

// Routes pour les Projets
Route::middleware('auth')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('project.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('project.store');
    Route::post('/projects/{project}/save', [ProjectController::class, 'save'])->name('project.save');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('project.destroy');

    // Route pour ajouter une étape à la roadmap globale (Admin seulement)
    Route::post('/task-templates', function (Request $request) {
        if (!auth()->user()->isAdmin()) abort(403);

        $request->validate([
            'label' => 'required|string|max:255',

        ]);
        $maxPosition = TaskTemplate::max('position') ?? 0;

        TaskTemplate::create([
        'label' => $request->label,
        'position' => $maxPosition + 1,
    ]);

        return back()->with('status', 'Étape ajoutée à la roadmap par défaut !');
    })->name('task-templates.store');
});
Route::post('/task-templates/{id}/move-up', function ($id) {
    $current = TaskTemplate::findOrFail($id);
    $previous = TaskTemplate::where('position', '<', $current->position)
        ->orderBy('position', 'desc')
        ->first();

    if ($previous) {
        $oldPos = $current->position;
        $current->update(['position' => $previous->position]);
        $previous->update(['position' => $oldPos]);
    }
    return back();
})->name('task-templates.move-up');

Route::post('/task-templates/{id}/move-down', function ($id) {
    $current = TaskTemplate::findOrFail($id);
    $next = TaskTemplate::where('position', '>', $current->position)
        ->orderBy('position', 'asc')
        ->first();

    if ($next) {
        $oldPos = $current->position;
        $current->update(['position' => $next->position]);
        $next->update(['position' => $oldPos]);
    }
    return back();
})->name('task-templates.move-down');

Route::delete('/task-templates/{template}', function (App\Models\TaskTemplate $template) {
    if (!auth()->user()->isAdmin()) abort(403);
    $template->delete();
    return back()->with('status', 'Étape supprimée de la roadmap par défaut.');
})->name('task-templates.destroy');

Route::post('/task-templates/publish', [ProjectController::class, 'publish'])
        ->name('task-templates.publish');

        Route::post('/projects/{project}/publish-roadmap', function (Project $project) {
    // On récupère les templates globaux
    $templates = TaskTemplate::orderBy('position')->get();

    foreach ($templates as $tmpl) {
        Task::create([
            'project_id' => $project->id,
            'label'      => $tmpl->label,
            'position'   => $tmpl->position,
            'is_roadmap' => true,
            'todo'       => true,
            'done'       => false,
        ]);
    }

    return back()->with('status', 'Roadmap ajoutée!');
})->name('task-templates.publish-single');
});

// Auth (login, register, logout…)
require __DIR__.'/auth.php';
