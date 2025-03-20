<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CustomerUser extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_customer',
        'state',
        'kind',
        'username',
        'password',
        'fname',
        'lname',
        'email',
        'telephone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    public function projectItems()
    {
        return $this->belongsToMany(ProjectItem::class, 'custuser_x_projectitem', 'id_custuser', 'id_projectitem');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'id_custuser');
    }

    public function requestMessages()
    {
        return $this->hasMany(RequestMessage::class, 'id_custuser');
    }
}
