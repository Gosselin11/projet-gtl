<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // 🔹 Affichage des projets
    public function index()
    {
        $projects = Project::with('tasks', 'tables.columns', 'tables.rows')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('project.index', compact('projects'));
    }

    // 🔹 Création d’un projet avec tableau fixe
    public function store()
    {
        $project = Project::create([
            'client' => 'Nom de l’entreprise'
        ]);

        $labels = [
            'Briefing définition du projet',
            'Visuel du site',
            'Hébergement',
            'Développement',
            'Tests',
            'Mise en ligne'
        ];

        foreach ($labels as $label) {
            $project->tasks()->create([
                'label' => $label,
                'todo' => true,
                'done' => false,
            ]);
        }

        return redirect()->route('project.index');
    }

    // 🔹 Sauvegarde / mise à jour des checkbox
    public function save(Request $request, Project $project)
    {
        foreach ($project->tasks as $task) {
            $task->update([
                'todo' => isset($request->todo[$task->id]),
                'done' => isset($request->done[$task->id]),
            ]);
        }

        return redirect()->route('project.index');
    }

    // 🔹 Suppression du projet
    public function destroy(Project $project)
    {
        $project->tasks()->delete();
        $project->delete();

        return redirect()->route('project.index');
    }
}
