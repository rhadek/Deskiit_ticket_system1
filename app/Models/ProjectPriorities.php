<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPriorities extends Model
{
    protected $table = 'project_priorities';

    protected $fillable = [
        'id_project',
        'name',
        'kind',
        'execution_time_limit',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }
}
