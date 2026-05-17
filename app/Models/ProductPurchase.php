<?php

namespace App\Models;

use App\Enums\PurchaseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPurchase extends Model
{
    protected $fillable = [
        'order_id',
        'product_title',
        'customer_product_link',
        'product_buy_amount',
        'customer_name',
        'phone_number',
        'pay_done_by',
        'ecommerce_platform',
        'receiver',
        'account_name',
        'status',
        'logistics_company',
        'logistics_tracking',
        'information_link',
        'courier_entry',
        'product_purchase_price',
        'shipping_and_extra_cost',
        'profit',
    ];

    protected $casts = [
        'product_buy_amount'      => 'decimal:2',
        'product_purchase_price'  => 'decimal:2',
        'shipping_and_extra_cost' => 'decimal:2',
        'profit'                  => 'decimal:2',
        'status'                  => PurchaseStatus::class,
        'created_at'              => 'datetime',
        'updated_at'              => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ── Auto-calculate profit ─────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (ProductPurchase $purchase) {
            $buy      = (float) $purchase->product_buy_amount;
            $cost     = (float) $purchase->product_purchase_price;
            $shipping = (float) $purchase->shipping_and_extra_cost;

            $purchase->profit = round($buy - $cost - $shipping, 2);
        });
    }
}
