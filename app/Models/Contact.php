<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'head_office',
        'shop_office',
        'licence',
        'email',
        'phone',
        'whatsapp',
        'facebook',
        'youtube',
        'instagram',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];
}
