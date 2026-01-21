<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['client', 'user_id'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function tables() {
    return $this->hasMany(Table::class);
}

public function roadmap()
{
    return $this->belongsToMany(TaskTemplate::class, 'project_task_template')
                ->withPivot('id', 'todo', 'done')
                ->withTimestamps();
}
}
