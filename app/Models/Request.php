<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_projectitem',
        'id_custuser',
        'inserted',
        'state',
        'kind',
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inserted' => 'datetime',
    ];

    public function projectItem()
    {
        return $this->belongsTo(ProjectItem::class, 'id_projectitem');
    }

    public function customerUser()
    {
        return $this->belongsTo(CustomerUser::class, 'id_custuser');
    }

    public function messages()
    {
        return $this->hasMany(RequestMessage::class, 'id_request');
    }

    public function reports()
    {
        return $this->hasMany(RequestReport::class, 'id_request');
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'request_x_media', 'id_request', 'id_media');
    }
}
