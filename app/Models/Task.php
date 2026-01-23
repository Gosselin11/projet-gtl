<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'project_id',
        'label',
        'position',
        'todo',
        'done',
        'is_roadmap'
    ];

    protected $attributes = [
'priority' => 'Basse',
'status' => 'En attente',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
