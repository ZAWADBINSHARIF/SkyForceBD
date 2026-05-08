<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsVideo extends Model
{
    protected $table = 'ads_videos';

    protected $fillable = ['video'];

    protected $casts = [
        'video'      => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
