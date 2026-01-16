<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Suivi projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a1a1a; color: white; }
        .card-project { max-width: 900px; background-color: #000; border: 1px solid #333; }
        input.form-control:focus { background-color: #222; color: white; border-color: #0dcaf0; box-shadow: none; }
        .table-title-input { font-size: 1.25rem; color: #0dcaf0 !important; font-weight: bold; }
    </style>
</head>
<body class="p-4">
<div class="container">

    {{-- Menu Administration --}}
    @if(auth()->check() && auth()->user()->isAdmin())
        <div class="mb-3 text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-warning">Tableau de bord</a>
        </div>
    @endif

    <h1 class="mb-4 text-info">Suivi de projet</h1>

    <div class="mb-4 d-flex justify-content-between align-items-center">
        {{-- Bouton Créer un nouveau dossier client --}}
        <form method="POST" action="{{ route('project.store') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg">Ajouter un projet</button>
        </form>

        {{-- Bouton Déconnexion --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-lg">Déconnexion</button>
        </form>
    </div>

    {{-- Messages Flash --}}
    @if (session('status'))
        <div id="flash-message" class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    {{-- Liste des projets --}}
    @foreach($projects as $project)
        <div class="mb-5 p-4 rounded mx-auto card-project shadow-lg">

            {{-- Barre d'outils interne au projet --}}
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-2">
                <h3 class="h5 m-0 text-uppercase text-secondary">Options du dossier</h3>
                <div class="d-flex gap-2">
                    {{-- Bouton pour ajouter le tableau fixe --}}
                    @if($project->tasks->count() == 0)
                        <form action="{{ route('table.addFixed', $project) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">+ Étapes Fixes</button>
                        </form>
                    @endif
                    {{-- Bouton pour ajouter un nouveau tableau dynamique --}}
                    <form action="{{ route('table.addCustom', $project) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">+ Tableau Vide</button>
                    </form>
                </div>
            </div>

            <form action="{{ route('project.save', $project) }}" method="POST">
                @csrf

                {{-- Nom du client --}}
                <div class="mb-4">
                    <label class="form-label text-success fw-bold small text-uppercase">Nom de l’entreprise / client</label>
                    <input type="text" name="client" class="form-control bg-dark text-white border-secondary" value="{{ $project->client }}">
                </div>

                {{-- AFFICHAGE DU TABLEAU FIXE (si existant) --}}
                @if($project->tasks->count() > 0)
                    <h5 class="text-white mb-2 small text-uppercase">Étapes de production</h5>
                    <table class="table table-bordered align-middle table-dark mb-4">
                        <thead>
                            <tr class="table-active">
                                <th class="text-center">Tâche</th>
                                <th class="text-center" style="width: 100px;">À faire</th>
                                <th class="text-center" style="width: 100px;">Fait</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->tasks as $task)
                                <tr>
                                    <td>{{ $task->label }}</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="todo[{{ $task->id }}]" class="todo-checkbox form-check-input" {{ $task->todo ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="done[{{ $task->id }}]" class="done-checkbox form-check-input" {{ $task->done ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- AFFICHAGE DES TABLEAUX DYNAMIQUES --}}
                @foreach($project->tables as $table)
                    <div class="mt-4 p-3 border border-secondary rounded bg-black">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            {{-- Input pour renommer le titre du tableau --}}
                            <input type="text" name="table_name[{{ $table->id }}]" value="{{ $table->name }}"
                                   class="form-control bg-transparent border-0 table-title-input w-50" placeholder="Nom du tableau">

                            <div class="btn-group shadow-sm">
                                <a href="{{ route('column.add', [$table, 'string']) }}" class="btn btn-sm btn-outline-info">+ Texte</a>
                                <a href="{{ route('column.add', [$table, 'checkbox']) }}" class="btn btn-sm btn-outline-warning">+ Checkbox</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-dark table-bordered m-0">
                                <thead>
                                    <tr class="table-active">
                                        @foreach($table->columns as $column)
                                            <th class="text-center p-0">
                                                <input type="text" name="col_name[{{ $column->id }}]" value="{{ $column->name }}"
                                                       class="form-control form-control-sm bg-transparent text-white border-0 text-center fw-bold py-2">
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < ($table->rows_count ?? 3); $i++)
                                        <tr>
                                            @foreach($table->columns as $column)
                                                @php
                                                    $cell = $column->cells()->where('row_index', $i)->first();
                                                @endphp
                                                <td class="text-center align-middle p-1">
                                                    @if($column->type == 'checkbox')
                                                        <input type="hidden" name="cells[{{ $column->id }}][{{ $i }}]" value="0">
                                                        <input type="checkbox" name="cells[{{ $column->id }}][{{ $i }}]" value="1"
                                                               class="form-check-input"
                                                               {{ ($cell && $cell->value == '1') ? 'checked' : '' }}>
                                                    @else
                                                        <input type="text" name="cells[{{ $column->id }}][{{ $i }}]"
                                                               value="{{ $cell->value ?? '' }}"
                                                               class="form-control form-control-sm bg-transparent text-white border-0 shadow-none">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('row.add', $table) }}" class="btn btn-sm btn-outline-success">+ Ajouter Ligne</a>

                            @if(auth()->user()->isAdmin())
                                <button type="button" class="btn btn-sm btn-outline-danger delete-table-btn" data-url="{{ route('table.destroy', $table) }}">
                                    Supprimer Tableau
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Actions du projet --}}
                <div class="d-flex justify-content-end mt-4 gap-2">
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-action="{{ route('project.destroy', $project) }}">
                            Supprimer Dossier
                        </button>
                    @endif
                    <button type="submit" class="btn btn-success px-5 fw-bold text-uppercase">Enregistrer</button>
                </div>
            </form>
        </div>
    @endforeach

</div>

{{-- Script JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Suppression (Projet ou Tableau)
    const genericForm = document.getElementById('delete-generic-form');

    document.querySelectorAll('.delete-btn, .delete-table-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const msg = this.classList.contains('delete-btn') ? 'Supprimer tout le dossier projet ?' : 'Supprimer ce tableau ?';
            if (confirm(msg)) {
                genericForm.action = this.dataset.action || this.dataset.url;
                genericForm.submit();
            }
        });
    });

    // Sync Tâches Fixes
    document.querySelectorAll('tbody tr').forEach(row => {
        const todo = row.querySelector('.todo-checkbox');
        const done = row.querySelector('.done-checkbox');
        if (todo && done) {
            const sync = () => {
                todo.disabled = done.checked;
                if (done.checked) todo.checked = false;
            };
            done.addEventListener('change', sync);
            sync();
        }
    });

    // Auto-hide messages flash
    const flash = document.getElementById('flash-message');
    if (flash) setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 500); }, 3000);
});
</script>

{{-- Formulaire de suppression unique --}}
<form id="delete-generic-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

</body>
</html>
