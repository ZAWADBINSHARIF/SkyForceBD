<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    public $timestamps = false;

    protected $fillable = ['image', 'link', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
        'created_at' => 'datetime',
    ];

    // ── Storage cleanup ──────────────────────────────────────────

    protected static function booted(): void
    {
        static::updating(function (Banner $banner) {
            if ($banner->isDirty('image')) {
                $old = $banner->getOriginal('image');
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
            }
        });

        static::deleting(function (Banner $banner) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
        });
    }
}
