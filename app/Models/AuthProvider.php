<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthProvider extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'provider',
        'provider_uid',
        'access_token',
    ];

    protected $hidden = ['access_token'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
