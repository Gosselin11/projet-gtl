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
    // Affichage de la liste des projets
    public function index()
    {
        // On charge les relations pour éviter les requêtes SQL inutiles
        $projects = Project::with(['tasks', 'tables.columns.cells'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('project.index', compact('projects'));
    }

    // Création initiale du projet (Dossier client vide)
    public function store()
    {
        Project::create(['client' => 'Nouveau Projet / Client']);
        return redirect()->route('project.index')->with('status', 'Nouveau dossier projet créé !');
    }

    // Ajouter le tableau des étapes FIXES à un projet
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
                'done' => false,
                'is_roadmap' => false,
            ]);
        }

        return back()->with('status', 'Tableau des étapes ajouté !');
    }

    // Ajouter un TABLEAU PERSONNALISÉ vide
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

    public function addTask(Project $project, Request $request){
        $isRoadmap = ($request->query('type')=== 'roadmap');
        $lastPos = $project->tasks()->where('is_roadmap', $isRoadmap)->max('position')?? 0;

        $project->tasks()->create([
            'label'=>$isRoadmap ? 'Nouvelle étape Roadmap' : 'Nouvelle étape fixe',
            'position'=>$lastPos + 1,
            'is_roadmap'=>$isRoadmap,
            'todo'=> true,
            'done'=> false
        ]);
        return back()->with('status', 'Ligne ajoutée avec succés');
    }

    public function addFixed(Project $project) {
    // 1. On récupère les templates de base (ceux qui ne sont pas dans la roadmap)
    $templates = TaskTemplate::where('is_roadmap', false)->orderBy('position')->get();

    // 2. On récupère AUSSI les templates de la roadmap actuelle
    $roadmapTemplates = TaskTemplate::where('is_roadmap', true)->orderBy('position')->get();

    $pos=1;
    // 3. On crée les tâches pour le projet (toutes en is_roadmap = false pour ce tableau)
    foreach ($templates->concat($roadmapTemplates) as $template) {
        $project->tasks()->create([
            'label' => $template->label,
            'position' => $template->position ?: $pos,
            'is_roadmap' => false, // On les met dans le tableau fixe du projet
            'todo' => true,
            'done' => false
        ]);
        $pos +=1;
    }

    return back()->with('status', 'Tableau de base chargé');
}

    // SAUVEGARDE GLOBALE (Tâches + Tableaux + Titres)
    public function save(Request $request, Project $project)
    {
        // Mise à jour du nom du client
        $project->update(['client' => $request->client]);

        // Sauvegarde des tâches fixes
        foreach ($project->tasks as $task) {
            $task->update([
                'todo' => isset($request->todo[$task->id]),
                'done' => isset($request->done[$task->id]),
            ]);
        }

        // Sauvegarde des noms de colonnes (si modifiés)
        if ($request->has('col_name')) {
            foreach ($request->col_name as $id => $name) {
                Column::where('id', $id)->update(['name' => $name]);
            }
        }

        // Sauvegarde des contenus de cellules
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

        if($request->has('task_label')){
            foreach ($request->task_label as $id =>$label){
                $project->tasks()->where('id',$id)->update([
                    'label'=> $label,
                    'position'=>$request->task_pos[$id] ?? 0,
                    'todo' => isset($request->todo[$id]),
                    'done'=> isset($request->done[$id]),
                ]);
            }

            if($request->has('task_pos')){
                foreach ($request->task_pos as $taskId=>$position){
                    $project->tasks()->where('id', $taskId)->update([
                        'position'=>$position,
                        'label' => $request->task_label[$taskId]
                    ]);
                }
            }
        }

        if ($request->has('status')){
            foreach ($request->status as $taskId => $statusValue){
                \App\Models\Task::where('id', $taskId)->update([
                    'status' => $statusValue,
                    'priority'=>$request->priority[$taskId] ?? 'Basse',
                    'label'=>$request->task_label[$taskId],
                    'position'=> $request->task_pos[$taskId],
                    'note' => $request->task_note[$taskId],
                ]);
            }
        }

        return redirect()->route('project.index')->with('status', 'Toutes les modifications ont été enregistrées !');
    }

    public function publishRoadmap(Project $project)
{
    // On récupère les modèles d'étapes
    $templates = \App\Models\TaskTemplate::orderBy('position')->get();

    // On crée des tâches INDIVIDUELLES marquées "is_roadmap"
    foreach ($templates as $tmpl) {
        $project->tasks()->create([
            'label'      => $tmpl->label,
            'position'   => $tmpl->position,
            'todo'       => true,
            'done'       => false,
            'is_roadmap' => true,
        ]);
    }

    return back()->with('status', 'Roadmap ajoutée !');
}

   public function publish()
{
    if (!auth()->user()->isAdmin()) abort(403);

    $projects = \App\Models\Project::all();
    $templates = \App\Models\TaskTemplate::orderBy('position')->get();

    foreach ($projects as $project) {
        foreach ($templates as $tmpl) {
            // On vérifie si la tâche n'existe pas déjà pour ne pas créer de doublons
            $exists = \App\Models\Task::where('project_id', $project->id)
                ->where('label', $tmpl->label)
                ->exists();

            if (!$exists) {
                \App\Models\Task::create([
                    'project_id' => $project->id,
                    'label'      => $tmpl->label,
                    'position'   => $tmpl->position,
                    'todo'       => true,
                    'done'       => false,
                ]);
            }
        }
    }

    return back()->with('status', 'La roadmap a été générée !');
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
        if (!auth()->user()->isAdmin()) {
        abort(403, "Action non autorisée.");
    }

        $project->delete();
        return redirect()->route('project.index')->with('status', 'Projet supprimé.');
    }

    public function destroyColumn(\App\Models\Column $column)
{
    if (!auth()->user()->isAdmin()) abort(403);

    $column->delete();
    return back()->with('status', 'Colonne supprimée.');
}

public function destroyRow(\App\Models\Table $table, $index)
{
    if (!auth()->user()->isAdmin()) abort(403);

    // Supprimer toutes les cellules appartenant à cette ligne (row_index)
    foreach ($table->columns as $column) {
        $column->cells()->where('row_index', $index)->delete();

        // Décaler les index des lignes suivantes vers le haut pour boucher le trou
        $column->cells()->where('row_index', '>', $index)->decrement('row_index');
    }

    // Diminuer le compteur de lignes du tableau s'il existe
    if ($table->rows_count > 0) {
        $table->decrement('rows_count');
    }

    return back()->with('status', 'Ligne supprimée.');
}
public function deleteRoadmap(Project $project)
{
    if (!auth()->user()->isAdmin()) {
        abort(403, "Action non autorisée.");
    }
    $project->tasks()->where('is_roadmap', true)->delete();
    return back()->with('status', 'Roadmap supprimée');
}

public function deleteFixedTasks(Project $project)
{
    if (!auth()->user()->isAdmin()) {
        abort(403, "Action non autorisée.");
    }
    $project->tasks()->where('is_roadmap', false)->delete();
    return back()->with('status', 'Étapes fixes supprimées');
}

// Supprimer une seule ligne
public function deleteTask(Task $task) {
    if (!auth()->user()->isAdmin()) {
        abort(403, "Action non autorisée.");
    }
    $projectId = $task->project_id;
    $isRoadmap = $task->is_roadmap;
    $task->delete();
    $tasks = Task::where('project_id', $projectId)->where('is_roadmap', $isRoadmap)->orderBy('position')->get();
    foreach($tasks as $index => $t){
        $t->update(['position'=>($index+1)]);
    }
    return back()->with('status', 'Ligne supprimée');
}

// Vider une section entière
public function deleteType(Project $project, $type) {
    if (!auth()->user()->isAdmin()) {
        abort(403, "Action non autorisée.");
    }
    $isRoadmap = ($type === 'roadmap');
    $project->tasks()->where('is_roadmap', $isRoadmap)->delete();
    return back()->with('status', 'Section vidée');
}

}
