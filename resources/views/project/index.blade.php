<!doctype html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <title>Suivi projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Couleurs dynamiques basées sur le thème */
        :root {
            --card-bg: #000000;
            --table-custom-bg: #050505;
        }

        [data-bs-theme="light"] {
            --card-bg: #ffffff;
            --table-custom-bg: #f8f9fa;
        }

        body { transition: background-color 0.3s ease; }

        .card-project {
            max-width: 900px;
            background-color: var(--card-bg);
            border: 1px solid var(--bs-border-color);
        }

        .custom-table-container {
            background-color: var(--table-custom-bg);
            border: 1px solid var(--bs-border-color);
        }

        /* Focus des champs */
        .form-control:focus {
            border-color: #0dcaf0;
            box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25);
        }

        .table-title-input {
            font-size: 1.25rem;
            color: #0dcaf0 !important;
            font-weight: bold;
        }

        /* Transition fluide pour le changement de thème */
        * { transition: background-color 0.2s ease, color 0.1s ease; }
    </style>
</head>
<body class="p-4">
<div class="container">

    <nav class="navbar mb-4">
        <div class="container-fluid p-0">
            <h1 class="navbar-brand text-info fw-bold m-0 fs-2">Suivi de projet</h1>
            <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="menuLateral">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title text-info">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-grid gap-3">
                <form method="POST" action="{{ route('project.store') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">+ Nouveau projet</button>
                </form>

                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('dashboard') }}" class="btn btn-warning w-100">Tableau de bord</a>
                @endif

                <hr>

                <div class="mb-3 text-center">
                    <label class="form-label small text-uppercase fw-bold opacity-50">Apparence</label>
                    <div class="btn-group w-100 shadow-sm">
                        <button class="btn btn-outline-secondary" onclick="setTheme('light')">Clair</button>
                        <button class="btn btn-outline-info" onclick="setTheme('dark')">Sombre</button>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div id="flash-message" class="alert alert-success shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    @foreach($projects as $project)
<div class="mb-5 p-4 rounded mx-auto card-project shadow-lg">
    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
        <h3 class="h6 m-0 text-uppercase text-secondary fw-bold">Options du dossier</h3>
        <div class="d-flex gap-2">
            <form action="{{ route('task-templates.publish-single', $project) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-info">+ Roadmap</button>
            </form>
           {{--  <form action="{{ route('table.addFixed', $project) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success">+ Fixes</button>
            </form> --}}
            <form action="{{ route('table.addCustom', $project) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">+ Vide</button>
            </form>
        </div>
    </div>

    <form action="{{ route('project.save', $project) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="form-label text-success fw-bold small text-uppercase">Client / Entreprise</label>
            <input type="text" name="client" class="form-control" value="{{ $project->client }}">
        </div>

        {{-- ROADMAP  --}}
@php $roadmapTasks = $project->tasks->where('is_roadmap', true)->sortBy('position'); @endphp
@if($roadmapTasks->isNotEmpty())
    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
        <h5 class="small text-uppercase opacity-75 text-info m-0 fw-bold">Roadmap de production</h5>

            <a href="{{ route('project.addTask', [$project, 'type' => 'roadmap']) }}" class="btn btn-xs btn-outline-info" style="font-size: 0.7rem;">+ Ajouter étape roadmap</a>

    </div>
    <table class="table table-bordered align-middle mb-2 shadow-sm">
        <thead class="table-light">
            <tr class="small text-uppercase">
                <th style="width: 50px;" class="text-center">Ordre</th>
                <th style="width: 250px;">Tâche</th>
                {{--  <th class="text-center" style="width: 70px;">À faire</th>
                <th class="text-center" style="width: 70px;">Fait</th>--}}
                <th class="text-center" style="width: 130px;">Priorité</th>
                <th class="text-center" style="width: 140px;">Status</th>
                <th>Note/Commentaire</th>
                @if(auth()->user()->isAdmin())
                <th style="width: 40px;"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($roadmapTasks as $task)
                <tr>
                    <td class="p-0"><input type="number" name="task_pos[{{ $task->id }}]" value="{{ $task->position }}" class="form-control form-control-sm border-0 bg-transparent text-center shadow-none"></td>
                    <td class="p-0"><input type="text" name="task_label[{{ $task->id }}]" value="{{ $task->label }}" class="form-control form-control-sm border-0 bg-transparent shadow-none"></td>
                    {{--  <td class="text-center"><input type="checkbox" name="todo[{{ $task->id }}]" class="todo-checkbox form-check-input" {{ $task->todo ? 'checked' : '' }}></td>
                    <td class="text-center"><input type="checkbox" name="done[{{ $task->id }}]" class="done-checkbox form-check-input" {{ $task->done ? 'checked' : '' }}></td>--}}

                    {{-- Colonne Priorité --}}
                    <td class="p-1">
                        <select name="priority[{{ $task->id }}]" class="form-select form-select-sm border-0 bg-light fw-bold" style="color:{{ $task->priority == 'Haute' ? '#dc3545' : ($task->priority == 'Moyenne' ? '#fd7e14' : '#6c757d') }};">
                            <option value="Basse" {{ ($task->priority ?? 'Basse')=='Basse' ? 'selected' : '' }}>Basse</option>
                            <option value="Moyenne" {{ $task->priority == 'Moyenne'?'selected':'' }}>Moyenne</option>
                            <option value="Haute"{{ $task->priority=='Haute'?'selected':'' }}>Haute</option>
                        </select>
                    </td>

                    {{-- Colonne Statut --}}
                    <td class="p-1">
                        <select name="status[{{ $task->id }}]" class="form-select form-select-sm fw-bold border-0 text-white" style="background-color: {{ $task->status == 'Terminé' ? '#198754': ($task->status == 'En cours' ? '#0dcaf0': ($task->status == 'Bloqué' ? '#dc3545' : '#6c757d')) }}">
                            <option value="En attente" {{ $task->status == 'En attente' ? 'selected': '' }}>En attente</option>
                            <option value="En cours"{{ $task->status == 'En cours' ? 'selected': ''}}>En cours</option>
                            <option value="Bloqué" {{ $task->status == 'Bloqué' ? 'selected': '' }}>Bloqué</option>
                            <option value="Terminé" {{ $task->status == 'Terminé' ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </td>
                    <td class="p-0">
                        <input type="text" name="task_note[{{ $task->id }}]" value="{{ $task->note }}" placeholder="" class="form-control form-control-sm border-0 bg-transparent shadow-none" style="font-style: italic; font-size: 0.85rem;">
                    </td>

                        @if(auth()->user()->isAdmin())
            <td class="text-center">
                <a href="{{ route('project.deleteTask', $task) }}"
                   class="text-danger opacity-50 hover-opacity-100"
                   onclick="return confirm('Supprimer cette ligne ?')">×</a>
            </td>
        @endif


                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-end mb-4">
        @if(auth()->user()->isAdmin())
        <a href="{{ route('project.deleteType', [$project, 'type' => 'roadmap']) }}" class="btn btn-xs text-danger border-0 small delete-table-btn" onclick="return confirm('Supprimer toute la roadmap ?')">Supprimer la roadmap</a>
        @endif
    </div>
@endif

{{-- ÉTAPES FIXES
@php $fixedTasks = $project->tasks->where('is_roadmap', false)->sortBy('position'); @endphp
@if($fixedTasks->isNotEmpty())
    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
        <h5 class="small text-uppercase opacity-75 text-success m-0 fw-bold">Étapes de base & Production</h5>
        <a href="{{ route('project.addTask', [$project, 'type' => 'fixed']) }}" class="btn btn-xs btn-outline-success" style="font-size: 0.7rem;">+ Ajouter ligne fixe</a>
    </div>
    <table class="table table-bordered align-middle mb-2 shadow-sm">
        <thead class="table-light">
            <tr class="small text-uppercase">
                <th style="width: 50px;" class="text-center">Ordre</th>
                <th>Tâche</th>
                <th class="text-center" style="width: 70px;">À faire</th>
                <th class="text-center" style="width: 70px;">Fait</th>
                <th style="width: 40px;"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($fixedTasks as $task)
                <tr>
                    <td class="p-0"><input type="number" name="task_pos[{{ $task->id }}]" value="{{ $task->position }}" class="form-control form-control-sm border-0 bg-transparent text-center shadow-none"></td>
                    <td class="p-0"><input type="text" name="task_label[{{ $task->id }}]" value="{{ $task->label }}" class="form-control form-control-sm border-0 bg-transparent shadow-none"></td>
                    <td class="text-center"><input type="checkbox" name="todo[{{ $task->id }}]" class="todo-checkbox form-check-input" {{ $task->todo ? 'checked' : '' }}></td>
                    <td class="text-center"><input type="checkbox" name="done[{{ $task->id }}]" class="done-checkbox form-check-input" {{ $task->done ? 'checked' : '' }}></td>
                    <td class="text-center">
                        <a href="{{ route('project.deleteTask', $task) }}" class="text-danger opacity-50" onclick="return confirm('Supprimer cette ligne ?')">×</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('project.deleteType', [$project, 'type' => 'fixed']) }}" class="btn btn-link btn-sm text-danger text-decoration-none opacity-50 small" onclick="return confirm('Supprimer toutes les étapes fixes ?')">Supprimer les étapes fixes</a>
    </div>
@endif--}}

        {{--TABLEAUX PERSONNALISÉS --}}
        @foreach($project->tables as $table)
            <div class="mt-4 p-3 rounded custom-table-container shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <input type="text" name="table_name[{{ $table->id }}]" value="{{ $table->name }}" class="form-control bg-transparent border-0 table-title-input w-50">
                    <div class="btn-group">
                        <a href="{{ route('column.add', [$table, 'string']) }}" class="btn btn-sm btn-outline-info">+ Texte</a>
                        <a href="{{ route('column.add', [$table, 'checkbox']) }}" class="btn btn-sm btn-outline-warning">+ Checkbox</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered m-0 align-middle" style="table-layout:auto;">
                        <thead>
                            <tr>
                                @foreach($table->columns as $column)
                                    <th class="p-0 border-bottom-0 position-relative" style="background-color: #000000; @if($column->type == 'checkbox') width: 1%; white-space: nowrap; @endif">

                                        {{-- Bouton supprimer colonne --}}
                                        @if(auth()->user()->isAdmin())
            <a href="{{ route('column.destroy', $column) }}"
               class="position-absolute text-danger text-decoration-none fw-bold"
               style="top: 0; left: 4px;  z-index: 20; font-size: 1.1rem; line-height: 1;"
               onclick="return confirm('Supprimer cette colonne ?')">
               &times;
            </a>
        @endif
                                        <input type="text" name="col_name[{{ $column->id }}]" value="{{ $column->name }}"
               class="form-control form-control-sm border-0 text-center fw-bold pt-3 pb-2 shadow-none"
               style="background-color: #000000; color: #ffffff; @if($column->type == 'checkbox') min-width: 60px; @else min-width: 150px; @endif">
                                    </th>
                                @endforeach
                                @if(auth()->user()->isAdmin())
    <th style="width: 40px; background-color: #000000; border-bottom: none;"></th>
@endif
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < ($table->rows_count ?? 3); $i++)
                                <tr>
                                    @foreach($table->columns as $column)
                                        @php $cell = $column->cells()->where('row_index', $i)->first(); @endphp
                                        <td class="text-center p-1">
                                            @if($column->type == 'checkbox')
                                                <input type="hidden" name="cells[{{ $column->id }}][{{ $i }}]" value="0">
                                                <input type="checkbox" name="cells[{{ $column->id }}][{{ $i }}]" value="1" class="form-check-input" {{ ($cell && $cell->value == '1') ? 'checked' : '' }}>
                                            @else
                                                <input type="text" name="cells[{{ $column->id }}][{{ $i }}]" value="{{ $cell->value ?? '' }}" class="form-control form-control-sm bg-transparent border-0 shadow-none text-center">
                                            @endif
                                        </td>
                                    @endforeach
                                    @if(auth()->user()->isAdmin())
                    <td class="text-center p-0">
                        <a href="{{ route('row.destroy', [$table, 'index' => $i]) }}"
                           class="text-danger opacity-50 text-decoration-none fw-bold"
                           onclick="return confirm('Supprimer cette ligne ?')">×</a>
                    </td>
                @endif
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('row.add', $table) }}" class="btn btn-xs text-success border-0 small">+ Ligne</a>
                    @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-xs text-danger border-0 small delete-table-btn" data-url="{{ route('table.destroy', $table) }}">Supprimer</button>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-end mt-4 gap-2">
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-action="{{ route('project.destroy', $project) }}">
                            Supprimer Dossier
                        </button>
                    @endif
                    <button type="submit" class="btn btn-outline-success px-5 fw-bold shadow">ENREGISTRER</button>
                </div>
            </form>
        </div>
    @endforeach


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // GESTION DU THÈME
    function setTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
    }
    const savedTheme = localStorage.getItem('theme') || 'dark';
    setTheme(savedTheme);

    // LOGIQUE DES PROJETS
    document.addEventListener('DOMContentLoaded', function () {
        const genericForm = document.getElementById('delete-generic-form');

        // Suppressions
        document.querySelectorAll('.delete-btn, .delete-table-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const msg = this.classList.contains('delete-btn') ? 'Supprimer tout le dossier ?' : 'Supprimer ce tableau ?';
                if (confirm(msg)) {
                    genericForm.action = this.dataset.action || this.dataset.url;
                    genericForm.submit();
                }
            });
        });

        // Sync Checkboxes Fixes
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

        // Flash Messages
        const flash = document.getElementById('flash-message');
        if (flash) setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 500); }, 3000);
    });
</script>

<form id="delete-generic-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

</body>
</html>
