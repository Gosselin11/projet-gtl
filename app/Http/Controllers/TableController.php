<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Column;
use App\Models\Row;
use App\Models\Cell;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function create() {
        return view('profile.edit'); // formulaire intégré dans profil
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'columns' => 'required|array|min:1',
            'rows' => 'required|integer|min:1',
        ]);

        $table = Table::create([
            'name' => $request->name,
            'project_id' => $request->project_id,
        ]);

        foreach ($request->columns as $index => $col) {
            Column::create([
                'table_id' => $table->id,
                'name' => $col['name'] ?? 'Nouvelle colonne',
                'type' => $col['type'],
                'order' => $index,
            ]);
        }

        for ($i = 0; $i < $request->rows; $i++) {
            $row = Row::create([
                'table_id' => $table->id,
                'order' => $i,
            ]);

            foreach ($table->columns as $column) {
                Cell::create([
                    'row_id' => $row->id,
                    'column_id' => $column->id,
                    'value' => null,
                    'checked' => null,
                ]);
            }
        }

        return redirect()
    ->route('project.index')
    ->with('success', 'Tableau créé avec succès');
    }

    public function show(Table $table) {
        $table->load('columns', 'rows.cells.column');
        return view('tableaux.show', compact('table'));
    }
}
