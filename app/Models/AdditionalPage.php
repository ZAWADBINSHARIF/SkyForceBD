<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalPage extends Model
{
    protected $fillable = ['name', 'slug', 'content', 'published', 'add_on_footer'];

    protected $casts = [
        'published'     => 'boolean',
        'add_on_footer' => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
}
