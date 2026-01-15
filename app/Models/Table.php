<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'project_id'];

    public function columns() {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    public function rows() {
        return $this->hasMany(Row::class)->orderBy('order');
    }

}
