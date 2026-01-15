<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cell extends Model {
    use HasFactory;

    protected $fillable = ['row_id', 'column_id', 'value', 'checked'];

    public function row() {
        return $this->belongsTo(Row::class);
    }

    public function column() {
        return $this->belongsTo(Column::class);
    }
}
