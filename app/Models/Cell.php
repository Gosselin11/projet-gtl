<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cell extends Model
{
    protected $fillable = ['column_id', 'row_index', 'value'];
}
