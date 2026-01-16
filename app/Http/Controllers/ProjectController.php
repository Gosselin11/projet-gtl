<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Table;
use App\Models\Cell;
use App\Models\Column;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Affichage de la liste des projets
    public function index()
    {
        // On charge les relations pour éviter les requêtes SQL inutiles
        $projects = Project::with(['tasks', 'tables.columns.cells'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('project.index', compact('projects'));
    }

    // 1. Création initiale du projet (Dossier client vide)
    public function store()
    {
        Project::create(['client' => 'Nouveau Projet / Client']);
        return redirect()->route('project.index')->with('status', 'Nouveau dossier projet créé !');
    }

    // 2. Ajouter le tableau des étapes FIXES à un projet
    public function addFixedTable(Project $project)
    {
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
                'done' => false
            ]);
        }

        return back()->with('status', 'Tableau des étapes ajouté !');
    }

    // 3. Ajouter un TABLEAU PERSONNALISÉ vide
    public function addCustomTable(Project $project)
    {
        $table = $project->tables()->create([
            'name' => 'Nouveau Tableau',
            'rows_count' => 3
        ]);

        // On crée une première colonne texte par défaut
        $table->columns()->create(['name' => 'Colonne 1', 'type' => 'string']);

        return back()->with('status', 'Tableau personnalisé ajouté !');
    }

    // Ajouter une colonne (Texte ou Checkbox)
    public function addColumn(Table $table, $type = 'string')
    {
        $table->columns()->create([
            'name' => ($type == 'checkbox' ? 'Fait ?' : 'Nouvelle colonne'),
            'type' => $type
        ]);
        return back();
    }

    // Ajouter une ligne supplémentaire
    public function addRow(Table $table)
    {
        $table->increment('rows_count');
        return back();
    }

    // SAUVEGARDE GLOBALE (Tâches + Tableaux + Titres)
    public function save(Request $request, Project $project)
    {
        // Mise à jour du nom du client
        $project->update(['client' => $request->client]);

        // 1. Sauvegarde des tâches fixes
        foreach ($project->tasks as $task) {
            $task->update([
                'todo' => isset($request->todo[$task->id]),
                'done' => isset($request->done[$task->id]),
            ]);
        }

        // 2. Sauvegarde des noms de colonnes (si modifiés)
        if ($request->has('col_name')) {
            foreach ($request->col_name as $id => $name) {
                Column::where('id', $id)->update(['name' => $name]);
            }
        }

        // 3. Sauvegarde des contenus de cellules
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

        return redirect()->route('project.index')->with('status', 'Toutes les modifications ont été enregistrées !');
    }

    // Supprimer un tableau spécifique
    public function destroyTable(Table $table)
    {
        $table->delete();
        return back()->with('status', 'Tableau supprimé.');
    }

    // Suppression complète du projet
    public function destroy(Project $project)
    {
        // Les relations (tasks, tables) seront supprimées par "cascade" si tes migrations sont bien faites
        $project->delete();
        return redirect()->route('project.index')->with('status', 'Projet supprimé.');
    }
}
