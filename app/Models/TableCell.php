<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableCell extends Model
{
    use HasFactory;

    protected $fillable = ['table_row_id', 'table_column_id', 'value'];

    public function row()
    {
        return $this->belongsTo(TableRow::class);
    }

    public function column()
    {
        return $this->belongsTo(TableColumn::class);
    }
}
