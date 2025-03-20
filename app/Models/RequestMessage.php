<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_request',
        'id_user',
        'id_custuser',
        'inserted',
        'state',
        'kind',
        'message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inserted' => 'datetime',
    ];

    /**
     * Get the request that owns the message.
     */
    public function request()
    {
        return $this->belongsTo(Request::class, 'id_request');
    }

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Get the customer user that owns the message.
     */
    public function customerUser()
    {
        return $this->belongsTo(CustomerUser::class, 'id_custuser');
    }
}
