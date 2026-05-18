<?php

namespace App\Models;

use App\Casts\PriceCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'product_name',
        'product_images',
        'product_description',
        'price',
        'old_price',
        'discount',
        'badge',
        'slug',
        'published',
    ];

    protected $casts = [
        'product_images' => 'array',   // JSONB <-> PHP array auto-cast
        'published'      => 'boolean',
        'price'          => PriceCast::class,
        'old_price'      => PriceCast::class,
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'wishlists')
            ->withTimestamps();
    }

    // ── Storage cleanup ──────────────────────────────────────────

    /**
     * Delete specific images from storage and remove them from the array.
     * Call this when the user removes individual images during an update.
     *
     * @param  array  $pathsToRemove  Storage paths to delete (subset of product_images)
     */
    public function removeImages(array $pathsToRemove): void
    {
        Storage::disk('public')->delete($pathsToRemove);

        $this->product_images = array_values(
            array_diff($this->product_images ?? [], $pathsToRemove)
        );

        $this->save();
    }

    /**
     * Delete ALL images from storage when the product itself is deleted.
     * Hooked via the model's booted() lifecycle below.
     */
    protected function deleteAllImages(): void
    {
        if (!empty($this->product_images)) {
            Storage::disk('public')->delete($this->product_images);
        }
    }

    // ── Model lifecycle hooks ────────────────────────────────────

    protected static function booted(): void
    {
        // When product_images is updated, delete the removed images from storage.
        static::updating(function (Product $product) {
            if (!$product->isDirty('product_images')) {
                return;
            }

            $old = $product->getOriginal('product_images') ?? [];
            $new = $product->product_images ?? [];

            // Cast old value — Laravel returns raw JSON string from getOriginal()
            if (is_string($old)) {
                $old = json_decode($old, true) ?? [];
            }

            $removed = array_diff($old, $new);

            if (!empty($removed)) {
                Storage::disk('public')->delete(array_values($removed));
            }
        });

        // When a product is deleted, purge all its images from storage.
        static::deleting(function (Product $product) {
            $product->deleteAllImages();
        });
    }
}
