<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ $table->name }}
        </h2>
    </x-slot>

    <div class="p-6 bg-white rounded shadow">
        <table class="border-collapse w-full border">
            <thead>
                <tr>
                    @foreach ($table->columns as $column)
                        <th class="border p-2 bg-gray-100">
                            <input
                                type="text"
                                value="{{ $column->name }}"
                                class="w-full border-none bg-transparent font-semibold"
                            >
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @for ($i = 0; $i < 3; $i++)
                    <tr>
                        @foreach ($table->columns as $column)
                            <td class="border p-2">
                                @if ($column->type === 'checkbox')
                                    <input type="checkbox">
                                @else
                                    <input
                                        type="{{ $column->type }}"
                                        placeholder="Votre texte ici"
                                        class="w-full border rounded p-1 text-sm placeholder-gray-400"
                                    >
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</x-app-layout>
