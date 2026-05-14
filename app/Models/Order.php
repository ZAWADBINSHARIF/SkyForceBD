<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\ShipmentType;
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
        'advance_payment'       => 'decimal:2',
        'due_payment'           => 'decimal:2',
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
