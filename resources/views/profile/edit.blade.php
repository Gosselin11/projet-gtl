<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


            <!-- FORMULAIRE ADMIN -->
            @if(auth()->user()->isAdmin())
                <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Créer un nouvel utilisateur
                    </h3>

                    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nom
                            </label>
                            <input type="text" name="name" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                                       dark:bg-gray-900 dark:text-white shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Email
                            </label>
                            <input type="email" name="email" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                                       dark:bg-gray-900 dark:text-white shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Mot de passe
                            </label>
                            <input type="password" name="password" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                                       dark:bg-gray-900 dark:text-white shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Rôle
                            </label>
                            <select name="role"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                                       dark:bg-gray-900 dark:text-white shadow-sm">
                                <option value="user">Utilisateur</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent
                                       rounded-md font-semibold text-white hover:bg-green-700">
                                Créer l’utilisateur
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if(auth()->user()->isAdmin())

    <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg mt-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Gestion des comptes
        </h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Rôle
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $user->role === 'admin'
                                        ? 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <form action="{{ route('users.destroy', $user) }}"
                                      method="POST"
                                      onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif


        </div>
    </div>

   <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg mt-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        Créer un tableau vierge
    </h3>

    <form method="POST" action="{{ route('tableaux.store') }}" class="space-y-4">
        @csrf



        {{-- Nom du tableau --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nom du tableau
            </label>
            <input type="text" name="name" required
                placeholder="Nom du tableau"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                       dark:bg-gray-900 dark:text-white shadow-sm">
        </div>

        {{-- Nombre de colonnes --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nombre de colonnes
            </label>
            <input type="number" id="nbColumns" min="1" value="3" required
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                       dark:bg-gray-900 dark:text-white shadow-sm">
        </div>

        {{-- Nombre de lignes --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nombre de lignes
            </label>
            <input type="number" name="rows" min="1" value="3" required
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                       dark:bg-gray-900 dark:text-white shadow-sm">
        </div>

        {{-- Colonnes dynamiques --}}
        <div id="columnsContainer" class="space-y-4"></div>

        {{-- Bouton --}}
        <div class="pt-4">
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent
                       rounded-md font-semibold text-white hover:bg-green-700">
                Créer le tableau
            </button>
        </div>
    </form>
</div>


<script>
    const nbColumnsInput = document.getElementById('nbColumns');
    const columnsContainer = document.getElementById('columnsContainer');

    function generateColumns() {
        const count = nbColumnsInput.value;
        columnsContainer.innerHTML = '';

        for (let i = 0; i < count; i++) {
            const div = document.createElement('div');
            div.className = 'grid grid-cols-1 md:grid-cols-2 gap-4';

            div.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nom colonne ${i + 1}
                    </label>
                    <input type="text"
                        name="columns[${i}][name]"
                        placeholder="Nouvelle colonne"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                               dark:bg-gray-900 dark:text-white shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Type
                    </label>
                    <select name="columns[${i}][type]"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700
                               dark:bg-gray-900 dark:text-white shadow-sm">
                        <option value="text">Texte</option>
                        <option value="number">Numéro</option>
                        <option value="checkbox">Checkbox</option>
                    </select>
                </div>
            `;

            columnsContainer.appendChild(div);
        }
    }

    nbColumnsInput.addEventListener('input', generateColumns);
    generateColumns();
</script>


</x-app-layout>
