<?php

namespace App\Observers;

use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->syncPaidAmount($transaction->order_id);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $this->syncPaidAmount($transaction->order_id);

        // If order_id changed, recalculate the old order too
        if ($transaction->wasChanged('order_id')) {
            $oldOrderId = $transaction->getOriginal('order_id');
            if ($oldOrderId && $oldOrderId !== $transaction->order_id) {
                $this->syncPaidAmount($oldOrderId);
            }
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }

    // ── Core logic ────────────────────────────────────────────────

    private function syncPaidAmount(int|string|null $orderId): void
    {
        if ($orderId === null) {
            return;
        }

        $paidAmount = Transaction::query()
            ->where('order_id', $orderId)
            ->where('status', TransactionStatus::Success->value)
            ->sum('payment_amount');

        $order = Order::query()->find($orderId);

        if (! $order) {
            return;
        }

        $totalPrice = (float) $order->total_price;

        $duePayment = max(
            $totalPrice - (float) $paidAmount,
            0
        );

        $order->update([
            'total_paid' => round((float) $paidAmount, 2),
            'due_payment' => round($duePayment, 2),
        ]);
    }
}
