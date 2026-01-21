<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded shadow-sm border border-green-200 dark:border-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Gestion des utilisateurs</h1>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rôle</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('users.updateRole', $user) }}" method="POST" class="flex gap-2">
                                                @csrf
                                                <select name="role" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-2 py-1 text-sm">
                                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                </select>
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm transition">Changer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            @if(auth()->user()->isAdmin())
            <div class="p-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-2">Actions de Synchronisation</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300 mb-4">
                </p>
                <form action="{{ route('task-templates.publish') }}" method="POST" onsubmit="return confirm('Diffuser la roadmap à tous les projets ?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 shadow-sm transition">
                        Envoyer la roadmap au suivi
                    </button>
                </form>
            </div>
            @endif


                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Configuration de la Roadmap par défaut</h1>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Position</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Étape</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
    @foreach(\App\Models\TaskTemplate::orderBy('position')->get() as $tmpl)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                <div class="flex items-center gap-2">
                    <form action="{{ route('task-templates.move-up', $tmpl->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-500">
                            +
                        </button>
                    </form>

                    <span class="w-4 text-center">{{ $tmpl->position }}</span>

                    <form action="{{ route('task-templates.move-down', $tmpl->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-500">
                            -
                        </button>
                    </form>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                {{ $tmpl->label }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <form action="{{ route('task-templates.destroy', $tmpl) }}" method="POST" onsubmit="return confirm('Supprimer cette étape ?');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-bold">
                        Supprimer
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>
                        </table>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-bold uppercase text-gray-500 mb-4">Ajouter une nouvelle étape</h4>
                        <form action="{{ route('task-templates.store') }}" method="POST" class="flex flex-wrap gap-4">
                            @csrf

                            <div class="flex-1 min-w-[200px]">
                                <input type="text" name="label" placeholder="Nom de l'étape (ex: Brief client)" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-semibold shadow-sm transition">
                                + Ajouter
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
