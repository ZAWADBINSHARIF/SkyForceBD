<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_number',
        'bank_transaction_id',
        'validation_id',
        'card_brand',
        'payment_method',
        'account_holder_name',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'card_issuer_country',
        'payment_amount',
        'store_amount',
        'status',
        'bank_transaction_image',
        'payment_info',
    ];

    protected $casts = [
        'status'         => TransactionStatus::class,
        'payment_info'   => 'array',
        'payment_amount' => 'decimal:2',
        'store_amount'   => 'decimal:2',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ── Storage cleanup ──────────────────────────────────────────

    protected static function booted(): void
    {
        // If the bank transaction image is replaced, delete the old file.
        static::updating(function (Transaction $transaction) {
            if ($transaction->isDirty('bank_transaction_image')) {
                $old = $transaction->getOriginal('bank_transaction_image');
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
            }
        });

        // When a transaction is deleted, remove its image from storage.
        static::deleting(function (Transaction $transaction) {
            if ($transaction->bank_transaction_image) {
                Storage::disk('public')->delete($transaction->bank_transaction_image);
            }
        });
    }
}
