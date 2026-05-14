<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Storage;

class Customer extends Authenticatable implements AuthenticatableContract
{
    protected $fillable = [
        'full_name',
        'phone_number',
        'address',
        'password_hash',
        'avatar_url',
        'email'
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $rememberTokenName = false;


    // ── Storage cleanup ──────────────────────────────────────────

    protected static function booted(): void
    {
        static::updating(function (Banner $banner) {
            if ($banner->isDirty('avatar_url')) {
                $old = $banner->getOriginal('avatar_url');
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
            }
        });

        static::deleting(function (Banner $banner) {
            if ($banner->avatar_url) {
                Storage::disk('public')->delete($banner->avatar_url);
            }
        });
    }

    // Tell Laravel to use password_hash column instead of password
    // public function getAuthPassword(): string
    // {
    //     return $this->password_hash;
    // }

    public function authProviders(): HasMany
    {
        return $this->hasMany(AuthProvider::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
