<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = ['office'];

    protected $casts = [
        'office'     => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
