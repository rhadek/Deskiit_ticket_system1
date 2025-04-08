<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestOffers extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_request',
        'name',
        'price',
        'file',
    ];

    /**
     * Požadavek, ke kterému nabídka patří
     */
    public function request()
    {
        return $this->belongsTo(Request::class, 'id_request');
    }
}
