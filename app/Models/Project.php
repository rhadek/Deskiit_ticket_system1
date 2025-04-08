<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_customer',
        'state',
        'kind',
        'name',
        'description',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    public function projectItems()
    {
        return $this->hasMany(ProjectItem::class, 'id_project');
    }
    public function projectPasswords()
    {
        return $this->hasMany(ProjectPassword::class, 'id_project');
    }
    public function projectPriorities()
    {
        return $this->hasMany(ProjectPriorities::class, 'id_project');
    }
}
