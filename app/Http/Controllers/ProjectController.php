<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Table;
use App\Models\Cell;
use App\Models\Column;
use App\Models\TaskTemplate;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['tasks', 'tables.columns.cells'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('project.index', compact('projects'));
    }

    // CRÉATION D'UN PROJET (Avec copie des templates)
    public function store(Request $request)
    {
        // 1. Créer le projet
        $project = Project::create([
            'client' => 'Nouveau Client',
            'user_id' => auth()->id(),
        ]);

        // 2. Récupérer les modèles d'étapes (Roadmap)
        $templates = TaskTemplate::orderBy('position', 'asc')->get();

        // 3. Copier les modèles dans le projet
        foreach ($templates as $template) {
            $project->tasks()->create([
                'label' => $template->label,
                'position' => $template->position,
                'todo' => false,
                'done' => false,
            ]);
        }

        return back()->with('status', 'Nouveau projet créé avec sa roadmap !');
    }

    // SAUVEGARDE DES MODIFICATIONS
    public function save(Request $request, Project $project)
    {
        $project->update(['client' => $request->client]);

        // Sauvegarde des tâches (Roadmap)
        if ($request->has('todo')) {
            foreach ($project->tasks as $task) {
                $task->update([
                    'todo' => isset($request->todo[$task->id]),
                    'done' => isset($request->done[$task->id]),
                ]);
            }
        }

        // Sauvegarde des cellules des tableaux personnalisés
        if ($request->has('cells')) {
            foreach ($request->cells as $columnId => $rows) {
                foreach ($rows as $rowIndex => $value) {
                    Cell::updateOrCreate(
                        ['column_id' => $columnId, 'row_index' => $rowIndex],
                        ['value' => $value]
                    );
                }
            }
        }

        return redirect()->route('project.index')->with('status', 'Modifications enregistrées.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('project.index')->with('status', 'Projet supprimé.');
    }
}
