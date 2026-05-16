<?php

namespace App\Models;

use App\Casts\PriceCast;
use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\ShipmentType;
use App\Enums\TransactionStatus;
use App\Enums\WorkProcess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'order_number',
        'order_receive_date',
        'order_status',
        'delivery_status',
        'delivery_date',
        'products',
        'work_process',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_remark',
        'employee_remark',
        'order_place_date',
        'purchase_product_link',
        'shipment_type',
        'order_call',
        'total_price',
        'shipping_charge',
        'product_weight',
        'advance_payment',
        'total_paid',
        'due_payment',
    ];

    protected $casts = [
        'products'              => 'array',
        'order_status'          => OrderStatus::class,
        'delivery_status'       => DeliveryStatus::class,
        'work_process'          => WorkProcess::class,
        'shipment_type'         => ShipmentType::class,
        'order_receive_date'    => 'datetime',
        'order_place_date'      => 'datetime',
        'delivery_date'         => 'date',
        'total_price'           => 'decimal:2',
        'shipping_charge'       => 'decimal:2',
        'advance_payment'       => PriceCast::class,
        'due_payment'           => 'decimal:2',
        'total_paid'            => 'decimal:2',
        'product_weight'        => 'decimal:3',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (Model $model) {
            if (empty($order->order_id)) {
                $model->order_number = "SKY" . "-" . now(6)->format('dmY') . "-" . strtoupper(Str::random(8));
            }
        });
    }

    public function getOrderNumberShortCodeAttribute(): string
    {
        return \Illuminate\Support\Str::afterLast($this->order_number, '-');
    }

    // ----------------------------
    // ORDER STATUS HELPERS
    // ----------------------------

    public function isOrderRequest(): bool
    {
        return $this->order_status === OrderStatus::OrderRequest;
    }

    public function isResponded(): bool
    {
        return $this->order_status === OrderStatus::Responsed;
    }

    public function isAccepted(): bool
    {
        return $this->order_status === OrderStatus::Accepted;
    }

    public function isRejected(): bool
    {
        return $this->order_status === OrderStatus::Rejected;
    }

    // ----------------------------
    // DELIVERY STATUS HELPERS
    // ----------------------------

    public function isPending(): bool
    {
        return $this->delivery_status === DeliveryStatus::Pending;
    }

    public function isProcessing(): bool
    {
        return $this->delivery_status === DeliveryStatus::Processing;
    }

    public function isShipped(): bool
    {
        return $this->delivery_status === DeliveryStatus::Shipped;
    }

    public function isDelivered(): bool
    {
        return $this->delivery_status === DeliveryStatus::Delivered;
    }

    public function isCancelled(): bool
    {
        return $this->delivery_status === DeliveryStatus::Cancelled;
    }

    // ----------------------------
    // TRANSACTION STATUS HELPERS
    // ----------------------------

    /**
     * Get the latest transaction for this order.
     */
    public function latestTransaction(): ?Transaction
    {
        return $this->transactions()
            ->latest()
            ->first();
    }

    public function isLatestTransactionStatusPending()
    {
        return $this->latestTransaction()?->status === TransactionStatus::Pending;
    }

    /**
     * Get the latest successful transaction.
     */
    public function latestSuccessfulTransaction(): ?Transaction
    {
        return $this->transactions()
            ->where('status', 'success')
            ->latest()
            ->first();
    }

    /**
     * Check if any transaction is successful.
     */
    public function hasPaidSuccessfully(): bool
    {
        return $this->transactions()
            ->where('status', 'success')
            ->exists();
    }

    /**
     * Check if there is a pending bank transfer waiting for verification.
     */
    public function hasPendingTransaction(): bool
    {
        return $this->transactions()
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Check if all transactions have failed.
     */
    public function hasOnlyFailedTransactions(): bool
    {
        $total  = $this->transactions()->count();
        $failed = $this->transactions()->where('status', 'failed')->count();

        return $total > 0 && $total === $failed;
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'order_call', 'id');
    }

    public function orderCall(): BelongsTo
    {
        return $this->user();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
