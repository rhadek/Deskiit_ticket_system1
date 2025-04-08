<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeTrackerSession extends Model
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
        'start_time',
        'end_time',
        'total_minutes',
        'completed',
        'report_created',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'completed' => 'boolean',
        'report_created' => 'boolean',
    ];

    /**
     * Get the request that the session belongs to.
     */
    public function request()
    {
        return $this->belongsTo(Request::class, 'id_request');
    }

    /**
     * Get the user that the session belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
