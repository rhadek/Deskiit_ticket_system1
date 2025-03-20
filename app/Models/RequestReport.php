<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestReport extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_report';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_request',
        'id_user',
        'inserted',
        'state',
        'kind',
        'work_start',
        'work_end',
        'work_total',
        'descript',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inserted' => 'datetime',
        'work_start' => 'datetime',
        'work_end' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class, 'id_request');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'requestreport_x_media', 'id_requestreport', 'id_media');
    }
}
