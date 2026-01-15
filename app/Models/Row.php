<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Row extends Model {
    use HasFactory;

    protected $fillable = ['table_id', 'order'];

    public function table() {
        return $this->belongsTo(Table::class);
    }

    public function cells() {
        return $this->hasMany(Cell::class);
    }
}
