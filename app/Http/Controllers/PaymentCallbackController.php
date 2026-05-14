<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Services\SSLCommerzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function success(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        // Validate with SSLCommerz before trusting the POST
        $ssl     = app(SSLCommerzService::class);
        $element = $ssl->getValidTransaction($tranId);

        if ($element === null) {
            return redirect()->route('checkout.fail');
        }

        if ($element->isHighRisk()) {
            Log::warning('SSLCommerz high risk transaction', ['tran_id' => $tranId]);
        }

        Transaction::query()
            ->where('transaction_number', $tranId)
            ->update([
                'status'              => TransactionStatus::Success->value,
                'validation_id'       => $element->valId,
                'bank_transaction_id' => $element->bankTranId,
                'payment_method'      => $element->cardBrand,
                'payment_amount'      => $element->amount,
                'store_amount'        => $element->storeAmount,
                'card_brand'          => $element->cardBrand,
                'card_issuer_country' => $element->cardIssuerCountry,
                'payment_info'        => $request->except('_token'),
            ]);

        session(['sslcz_tran_id' => $tranId]);

        return redirect()->route('checkout.success');
    }

    public function fail(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        Transaction::query()
            ->where('transaction_number', $tranId)
            ->update([
                'status' => TransactionStatus::Failed->value,
            ]);

        return redirect()->route('checkout.fail');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        Transaction::query()
            ->where('transaction_number', $tranId)
            ->update([
                'status' => TransactionStatus::Canceled->value
            ]);

        return redirect()->route('checkout.cancel');
    }
}
