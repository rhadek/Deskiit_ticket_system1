<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state',
        'kind',
        'name',
        'ic',
    ];

    public function customerUsers()
    {
        return $this->hasMany(CustomerUser::class, 'id_customer');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'id_customer');
    }
}
