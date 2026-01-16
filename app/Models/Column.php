<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    protected $fillable = ['table_id', 'name', 'type'];

public function cells() {
    return $this->hasMany(Cell::class);
}
}
