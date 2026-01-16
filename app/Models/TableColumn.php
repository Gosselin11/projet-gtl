<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableColumn extends Model
{
    use HasFactory;

    protected $fillable = ['table_id', 'name', 'type', 'position'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function cells()
    {
        return $this->hasMany(TableCell::class);
    }
}
