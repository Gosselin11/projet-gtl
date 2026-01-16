<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['project_id', 'name', 'rows_count'];

public function columns() {
    return $this->hasMany(Column::class);
}
}
