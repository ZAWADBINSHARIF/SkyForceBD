<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalPage extends Model
{
    protected $fillable = ['name', 'slug', 'content'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
