<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;



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

    // Tell Laravel to use password_hash column instead of password
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function authProviders(): HasMany
    {
        return $this->hasMany(AuthProvider::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
