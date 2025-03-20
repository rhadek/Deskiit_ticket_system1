<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_project',
        'state',
        'kind',
        'name',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_x_projectitem', 'id_projectitem', 'id_user');
    }

    public function customerUsers()
    {
        return $this->belongsToMany(CustomerUser::class, 'custuser_x_projectitem', 'id_projectitem', 'id_custuser');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'id_projectitem');
    }
}
