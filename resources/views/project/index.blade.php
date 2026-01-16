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
                    @if($project->tasks->count() == 0)
                        <form action="{{ route('table.addFixed', $project) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">+ Fixes</button>
                        </form>
                    @endif
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

                @if($project->tasks->count() > 0)
                    <h5 class="mb-2 small text-uppercase opacity-75">Étapes de production</h5>
                    <table class="table table-bordered align-middle mb-4 shadow-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Tâche</th>
                                <th class="text-center" style="width: 80px;">À faire</th>
                                <th class="text-center" style="width: 80px;">Fait</th>
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

                @foreach($project->tables as $table)
                    <div class="mt-4 p-3 rounded custom-table-container shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <input type="text" name="table_name[{{ $table->id }}]" value="{{ $table->name }}"
                                   class="form-control bg-transparent border-0 table-title-input w-50">

                            <div class="btn-group">
                                <a href="{{ route('column.add', [$table, 'string']) }}" class="btn btn-sm btn-outline-info">+ Texte</a>
                                <a href="{{ route('column.add', [$table, 'checkbox']) }}" class="btn btn-sm btn-outline-warning">+ Checkbox</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered m-0 align-middle">
                                <thead class="table-secondary bg-opacity-10">
                                    <tr>
                                        @foreach($table->columns as $column)
                                            <th class="p-0 border-bottom-0">
                                                <input type="text" name="col_name[{{ $column->id }}]" value="{{ $column->name }}"
                                                       class="form-control form-control-sm bg-transparent border-0 text-center fw-bold py-2 shadow-none">
                                            </th>
                                        @endforeach
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
                                                        <input type="checkbox" name="cells[{{ $column->id }}][{{ $i }}]" value="1"
                                                               class="form-check-input" {{ ($cell && $cell->value == '1') ? 'checked' : '' }}>
                                                    @else
                                                        <input type="text" name="cells[{{ $column->id }}][{{ $i }}]"
                                                               value="{{ $cell->value ?? '' }}"
                                                               class="form-control form-control-sm bg-transparent border-0 shadow-none text-center">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('row.add', $table) }}" class="btn btn-xs text-success border-0 small">+ Ligne</a>
                            @if(auth()->user()->isAdmin())
                                <button type="button" class="btn btn-xs text-danger border-0 small delete-table-btn" data-url="{{ route('table.destroy', $table) }}">
                                    Supprimer
                                </button>
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
                    <button type="submit" class="btn btn-success px-5 fw-bold shadow">ENREGISTRER</button>
                </div>
            </form>
        </div>
    @endforeach

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. GESTION DU THÈME
    function setTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
    }

    // Charger le thème au démarrage
    const savedTheme = localStorage.getItem('theme') || 'dark';
    setTheme(savedTheme);

    // 2. LOGIQUE DES PROJETS
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
