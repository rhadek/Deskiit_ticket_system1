<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
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
        'file',
    ];

    public function requests()
    {
        return $this->belongsToMany(Request::class, 'request_x_media', 'id_media', 'id_request');
    }

    public function requestMessages()
    {
        return $this->belongsToMany(RequestMessage::class, 'requestmessage_x_media', 'id_media', 'id_requestmessage');
    }

    public function requestReports()
    {
        return $this->belongsToMany(RequestReport::class, 'requestreport_x_media', 'id_media', 'id_requestreport');
    }
}
