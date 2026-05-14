<?php

use App\Models\Transaction;
use Livewire\Component;

new class extends Component
{
    public string $state          = 'success'; // success | fail | cancel
    public string $orderNumber    = '';
    public string $transactionNumber = '';

    public function mount(string $state = 'success'): void
    {
        $this->state = in_array($state, ['success', 'fail', 'cancel'], strict: true)
            ? $state
            : 'success';

        // if ($this->state === 'success') {
        //     // Pull from session — SSLCommerz posts back tran_id and val_id
        //     $tranId = session('sslcz_tran_id');

        //     if ($tranId) {
        //         $transaction = Transaction::query()
        //             ->where('transaction_number', $tranId)
        //             ->with('order')
        //             ->first();

        //         if ($transaction) {
        //             $this->transactionNumber = $transaction->transaction_number;
        //             $this->orderNumber       = $transaction->order?->order_number ?? '';
        //         }

        //         session()->forget('sslcz_tran_id');
        //     }
        // }
    }

    public function goHome(): void
    {
        $this->redirect(route('home'), navigate: true);
    }

    public function goOrders(): void
    {
        $this->redirect(route('orders.index'), navigate: true);
    }
};
?>

<div class="min-h-[60vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        {{-- ── Success ── --}}
        @if ($state === 'success')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            {{-- Top accent --}}
            <div class="h-1.5 w-full bg-emerald-500 rounded-t-2xl"></div>

            <div class="px-8 py-10 text-center">

                {{-- Icon --}}
                <div class="w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h1 class="text-xl font-bold text-gray-900 mb-1">Payment Successful</h1>
                <p class="text-sm text-gray-500 mb-8">Your order has been placed. We'll process it shortly.</p>

                {{-- Details --}}
                <div class="bg-gray-50 rounded-xl divide-y divide-gray-100 text-left mb-8">

                    @if ($orderNumber)
                    <div class="flex items-center justify-between px-4 py-3.5">
                        <span class="text-xs font-medium text-gray-500">Order number</span>
                        <span class="text-sm font-bold text-gray-900 font-mono tracking-wide">
                            {{ $orderNumber }}
                        </span>
                    </div>
                    @endif

                    @if ($transactionNumber)
                    <div class="flex items-center justify-between px-4 py-3.5">
                        <span class="text-xs font-medium text-gray-500">Transaction ID</span>
                        <span class="text-sm font-semibold text-gray-700 font-mono tracking-wide">
                            {{ $transactionNumber }}
                        </span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between px-4 py-3.5">
                        <span class="text-xs font-medium text-gray-500">Status</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Confirmed
                        </span>
                    </div>

                    <div class="flex items-center justify-between px-4 py-3.5">
                        <span class="text-xs font-medium text-gray-500">Date</span>
                        <span class="text-sm text-gray-700">{{ now()->format('d M Y, h:i A') }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2.5">
                    <button
                        wire:click="goOrders"
                        type="button"
                        class="btn-primary w-full py-3 rounded-xl font-semibold text-sm">
                        View My Orders
                    </button>
                    <button
                        wire:click="goHome"
                        type="button"
                        class="w-full py-3 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                        Back to Home
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Fail ── --}}
        @elseif ($state === 'fail')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="h-1.5 w-full bg-red-500 rounded-t-2xl"></div>

            <div class="px-8 py-10 text-center">

                <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h1 class="text-xl font-bold text-gray-900 mb-1">Payment Failed</h1>
                <p class="text-sm text-gray-500 mb-8">
                    Your payment could not be processed.<br>No amount has been deducted.
                </p>

                <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3.5 mb-8 text-left">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-red-700 mb-0.5">What went wrong?</p>
                            <p class="text-xs text-red-600 leading-relaxed">
                                This can happen due to insufficient balance, incorrect card details, or a temporary gateway issue. Please try again or use a different method.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2.5">
                    <button
                        wire:click="goOrders"
                        type="button"
                        class="btn-primary w-full py-3 rounded-xl font-semibold text-sm">
                        Try Again
                    </button>
                    <button
                        wire:click="goHome"
                        type="button"
                        class="w-full py-3 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                        Back to Home
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Cancel ── --}}
        @elseif ($state === 'cancel')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="h-1.5 w-full bg-amber-400 rounded-t-2xl"></div>

            <div class="px-8 py-10 text-center">

                <div class="w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h1 class="text-xl font-bold text-gray-900 mb-1">Payment Cancelled</h1>
                <p class="text-sm text-gray-500 mb-8">
                    You cancelled the payment.<br>Your order has not been placed.
                </p>

                <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3.5 mb-8 text-left">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-amber-700 mb-0.5">Changed your mind?</p>
                            <p class="text-xs text-amber-700 leading-relaxed">
                                Your cart is still saved. You can go back and complete your purchase whenever you're ready.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2.5">
                    <button
                        wire:click="goOrders"
                        type="button"
                        class="btn-primary w-full py-3 rounded-xl font-semibold text-sm">
                        Continue Shopping
                    </button>
                    <button
                        wire:click="goHome"
                        type="button"
                        class="w-full py-3 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                        Back to Home
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>