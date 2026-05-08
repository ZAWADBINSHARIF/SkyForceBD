<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'full_name',
        'phone_number',
        'address',
        'password_hash',
        'avatar_url',
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function authProviders(): HasMany
    {
        return $this->hasMany(AuthProvider::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
