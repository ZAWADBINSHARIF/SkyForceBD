<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;


/** @property-read \Illuminate\Database\Eloquent\Collection $orders */
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
        static::updating(function (Customer $customer) {
            if ($customer->isDirty('avatar_url')) {
                $old = $customer->getOriginal('avatar_url');
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
            }
        });

        static::deleting(function (Customer $customer) {
            if ($customer->avatar_url) {
                Storage::disk('public')->delete($customer->avatar_url);
            }
        });
    }

    // Tell Laravel to use password_hash column instead of password
    public function getAuthPassword(): ?string
    {
        return $this->password_hash;
    }

    /**
     * Check if a product is in the customer's wishlist.
     */
    public function hasWishlisted(int $productId): bool
    {
        return $this->wishlists()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Add a product to wishlist. Safe to call multiple times.
     */
    public function addToWishlist(int $productId): void
    {
        $this->wishlists()->firstOrCreate([
            'product_id' => $productId,
        ]);
    }

    /**
     * Remove a product from wishlist.
     */
    public function removeFromWishlist(int $productId): void
    {
        $this->wishlists()
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Toggle wishlist — add if not present, remove if present.
     * Returns true if added, false if removed.
     */
    public function toggleWishlist(int $productId): bool
    {
        if ($this->hasWishlisted($productId)) {
            $this->removeFromWishlist($productId);
            return false;
        }

        $this->addToWishlist($productId);
        return true;
    }

    public function authProviders(): HasMany
    {
        return $this->hasMany(AuthProvider::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists')
            ->withTimestamps();
    }
}
