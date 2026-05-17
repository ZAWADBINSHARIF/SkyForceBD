<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\SSLCommerzService;
use DateTimeZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function success(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        $ssl     = app(SSLCommerzService::class);
        $element = $ssl->getValidTransaction($tranId);

        if ($element === null) {
            return redirect()->route('checkout.fail');
        }

        if ($element->isHighRisk()) {
            Log::warning('SSLCommerz high risk transaction', ['tran_id' => $tranId]);
        }

        $transaction = Transaction::query()
            ->where('transaction_number', $tranId)
            ->first();

        if ($transaction) {
            $transaction->update([
                'status'              => TransactionStatus::Success,
                'validation_id'       => $element->valId,
                'bank_transaction_id' => $element->bankTranId,
                'payment_method'      => $element->cardType,
                'payment_amount'      => $element->amount,
                'store_amount'        => $element->storeAmount,
                'card_brand'          => $element->cardBrand,
                'card_issuer_country' => $element->cardIssuerCountry,
                'payment_info'        => $request->except('_token'),
            ]);

            $order = Order::find($transaction->order_id);

            if ($order === null) {
                session(['sslcz_tran_id' => $tranId]);
                return redirect()->route('checkout.success');
            }

            if ($order->order_status === null && $order->delivery_status === null) {
                $order->update([
                    'order_status'    => OrderStatus::OrderRequest,
                    'delivery_status' => DeliveryStatus::Pending,
                    'order_receive_date' => now()->timezone('Asia/Dhaka')
                ]);
            } elseif (
                $order->order_status    === OrderStatus::Responsed &&
                $order->delivery_status === DeliveryStatus::Pending
            ) {
                $order->update([
                    'order_status'    => OrderStatus::Accepted,
                    'delivery_status' => DeliveryStatus::Processing,
                    'order_place_date' => now()->timezone('Asia/Dhaka')
                ]);
            }
        }

        session(['sslcz_tran_id' => $tranId]);

        return redirect()->route('checkout.success');
    }

    public function fail(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        $transaction = Transaction::query()
            ->where('transaction_number', $tranId)
            ->first();

        if ($transaction) {
            $transaction->update([
                'status' => TransactionStatus::Failed->value,
            ]);
        }

        return redirect()->route('checkout.fail');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        $transaction = Transaction::query()
            ->where('transaction_number', $tranId)
            ->first();

        if ($transaction) {
            $transaction->update(
                [
                    'status' => TransactionStatus::Canceled->value
                ]
            );
        }

        return redirect()->route('checkout.cancel');
    }
}
