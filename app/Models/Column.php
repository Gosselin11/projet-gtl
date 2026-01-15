<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Column extends Model {
    use HasFactory;

    protected $fillable = ['table_id', 'name', 'type', 'order'];

    public function table() {
        return $this->belongsTo(Table::class);
    }
}
