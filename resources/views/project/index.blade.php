<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Suivi projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">

    {{-- Bouton tableau de bord visible seulement pour les admins --}}
    @if(auth()->check() && auth()->user()->isAdmin())
        <div class="mb-3 text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-warning">
                Tableau de bord
            </a>
        </div>
    @endif

    <h1 class="mb-4">Suivi de projet</h1>

    <div class="mb-4 d-flex justify-content-between align-items-center">

        {{-- Bouton Ajouter Tableau --}}
        <form method="POST" action="{{ route('project.store') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg">
                Ajouter tableau
            </button>
        </form>

        {{-- Bouton Déconnexion --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-lg">
                Déconnexion
            </button>
        </form>

    </div>

    {{-- Message de confirmation --}}
    @if (session('status'))
        <div id="flash-message" class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    {{-- Boucle sur tous les projets --}}
    @foreach($projects as $project)
        <div class="mb-5 p-3 border rounded mx-auto"
             style="max-width: 900px; background-color: #000;">

            <form action="{{ route('project.save', $project) }}" method="POST">
                @csrf

                {{-- Nom du client --}}
                <div class="mb-3">
                    <label class="form-label text-success fw-bold">
                        Nom de l’entreprise / client
                    </label>
                    <input type="text"
                           name="client"
                           class="form-control"
                           value="{{ $project->client }}">
                </div>

                {{-- TABLEAU FIXE DES TÂCHES --}}
                <table class="table table-bordered align-middle"
                       style="background-color: #000; color: #fff;">
                    <thead>
                        <tr>
                            <th class="text-center">Tâche</th>
                            <th class="text-center">À faire</th>
                            <th class="text-center">Fait</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->tasks as $task)
                            <tr>
                                <td>{{ $task->label }}</td>
                                <td class="text-center">
                                    <input type="checkbox"
                                           name="todo[{{ $task->id }}]"
                                           class="todo-checkbox"
                                           {{ $task->todo ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox"
                                           name="done[{{ $task->id }}]"
                                           class="done-checkbox"
                                           {{ $task->done ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
@if($project->tables->where(fn($t) => $t->rows->count() > 0)->count())

    <h5 class="text-success mt-4">
        Tableaux remplis
    </h5>

    @foreach($project->tables as $table)
        @if($table->rows->count() > 0)

            <div class="mb-4 p-3 border rounded"
                 style="background-color:#0b2e13; color:#fff;">

                <strong>{{ $table->name }}</strong>

                <table class="table table-bordered mt-2 text-white">
                    <thead>
                        <tr>
                            @foreach($table->columns as $column)
                                <th class="text-center">
                                    {{ $column->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                </table>

            </div>

        @endif
    @endforeach
@endif
{{-- TABLEAUX DYNAMIQUES DU PROJET --}}
@if($project->tables->where(fn($t) => $t->rows->count() === 0)->count())

    <h5 class="text-info mt-4 mb-2">
        Tableaux du projet
    </h5>

    @foreach($project->tables as $table)

        <div class="mb-4 p-3 border rounded"
             style="background-color:#111; color:#fff;">

            <strong class="text-warning">
                {{ $table->name }}
            </strong>

            <table class="table table-bordered mt-2 text-white">
                <thead>
                    <tr>
                        @foreach($table->columns as $column)
                            <th class="text-center">
                                {{ $column->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            </table>

        </div>

    @endforeach

@else
    <p class="text-muted mt-3">
        Aucun tableau dynamique pour ce projet.
    </p>
@endif



                <div class="d-flex justify-content-end mt-3 gap-2">

                    {{-- Bouton Supprimer --}}
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <button type="button"
                                class="btn btn-danger delete-btn"
                                data-action="{{ route('project.destroy', $project) }}">
                            Supprimer
                        </button>
                    @endif

                    {{-- Bouton Enregistrer --}}
                    <button type="submit" class="btn btn-success">
                        Enregistrer
                    </button>
                </div>

            </form>
        </div>
    @endforeach

</div>

{{-- Script JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Confirmation suppression
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            if (confirm('Voulez-vous vraiment supprimer ce tableau ?')) {
                const form = document.getElementById('delete-form');
                form.action = this.dataset.action;
                form.submit();
            }
        });
    });

    // Synchronisation À faire / Fait
    document.querySelectorAll('tbody tr').forEach(function (row) {
        const todo = row.querySelector('.todo-checkbox');
        const done = row.querySelector('.done-checkbox');

        if (!todo || !done) return;

        function sync() {
            if (done.checked) {
                todo.checked = false;
                todo.disabled = true;
            } else {
                todo.disabled = false;
            }
        }

        sync();
        done.addEventListener('change', sync);
    });

    // Flash message disparition auto
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.5s ease';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }, 3000);
    }

});
</script>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

</body>
</html>
