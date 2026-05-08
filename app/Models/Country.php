<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['country'];

    protected $casts = [
        'country'    => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
