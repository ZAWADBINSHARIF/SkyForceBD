<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function success(Request $request): RedirectResponse
    {
        $tranId = $request->input('tran_id');

        // Validate with SSLCommerz IPN here...

        // Update transaction status
        Transaction::where('transaction_number', $tranId)
            ->update(['status' => 'success']);

        // Store tran_id in session so the Livewire page can pick it up
        session(['sslcz_tran_id' => $tranId]);

        return redirect()->route('checkout.success');
    }

    public function fail(Request $request): RedirectResponse
    {
        return redirect()->route('checkout.fail');
    }

    public function cancel(Request $request): RedirectResponse
    {
        return redirect()->route('checkout.cancel');
    }
}
