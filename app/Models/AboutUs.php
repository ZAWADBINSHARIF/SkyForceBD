<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['image_url', 'heading', 'heading_highlight', 'body', 'features', 'cta_label', 'cta_link'])]
class AboutUs extends Model
{
    protected $casts = [
        'features'     => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
