<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
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
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projectItems()
    {
        return $this->belongsToMany(ProjectItem::class, 'user_x_projectitem', 'id_user', 'id_projectitem');
    }

    public function requestMessages()
    {
        return $this->hasMany(RequestMessage::class, 'id_user');
    }

    public function requestReports()
    {
        return $this->hasMany(RequestReport::class, 'id_user');
    }
}
