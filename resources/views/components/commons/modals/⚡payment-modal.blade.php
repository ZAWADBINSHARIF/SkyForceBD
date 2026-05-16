<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{

    public bool $showPaymentModal = true;
    public string $selectedMethod = '';

    public ?string $callSslCommerzPaymentEvent, $callBkashPaymentEvent, $callBankPaymentEvent = null;

    public array $BkashEventPayload, $SslCommerzEventPayload, $BankEventPayload, $method = [];

    #[On('open-payment-modal')]
    public function open(
        ?string $callSslCommerzPaymentEvent = null,
        ?string $callBkashPaymentEvent = null,
        ?string $callBankPaymentEvent = null,
        array $BkashEventPayload = [],
        array $SslCommerzEventPayload = [],
        array $BankEventPayload = [],
        array $method = []
    ): void {
        $this->callSslCommerzPaymentEvent = $callSslCommerzPaymentEvent;
        $this->callBkashPaymentEvent = $callBkashPaymentEvent;
        $this->callBankPaymentEvent = $callBankPaymentEvent;

        $this->BkashEventPayload = $BkashEventPayload;
        $this->SslCommerzEventPayload = $SslCommerzEventPayload;
        $this->BankEventPayload = $BankEventPayload;

        $this->method = $method;

        $this->showPaymentModal = true;
    }

    public function openModal(): void
    {
        $this->showPaymentModal = true;
        $this->selectedMethod   = '';
    }

    public function closeModal(): void
    {
        $this->showPaymentModal = false;
        $this->selectedMethod   = '';
    }

    public function selectMethod(string $method): void
    {
        $this->selectedMethod = $method;
    }

    public function proceedPayment(): void
    {
        if (empty($this->selectedMethod)) {
            return;
        }

        if ($this->callEvent) {
            $this->dispatch($this->callEvent, ...$this->eventPayload);
        }

        // TODO: handle each method
        match ($this->selectedMethod) {
            'bkash'       => $this->redirect(route('payment.bkash')),
            'sslcommerz'  => $this->handlePaymentSslCommerz(),
            'bank'        => $this->redirect(route('payment.bank')),
            default       => null,
        };
    }
};
?>

<div>
    @if ($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:click.self="closeModal">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeModal"></div>

        {{-- Panel --}}
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl z-10 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Select Payment Method</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Total: <span class="font-semibold text-gray-700">৳65</span>
                    </p>
                </div>
                <button wire:click="closeModal" type="button"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Methods --}}
            <div class="px-5 py-4 space-y-3">

                {{-- bKash --}}
                <button wire:click="selectMethod('bkash')" type="button" class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl border-2 transition-all duration-150
                    {{ $selectedMethod === 'bkash'
                        ? 'border-pink-500 bg-pink-50'
                        : 'border-gray-100 bg-gray-50 hover:border-gray-200 hover:bg-white' }}">

                    {{-- bKash logo mark --}}
                    <div class="w-14 h-14 rounded-xl bg-[#E2136E] flex items-center justify-center shrink-0">
                        <span class="text-white font-black text-xs tracking-tight">bKash</span>
                    </div>

                    <div class="text-left flex-1">
                        <p class="text-sm font-semibold text-gray-800">bKash</p>
                        <p class="text-xs text-gray-400">Mobile banking · instant</p>
                    </div>

                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors
                    {{ $selectedMethod === 'bkash' ? 'border-pink-500 bg-pink-500' : 'border-gray-300' }}">
                        @if ($selectedMethod === 'bkash')
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        @endif
                    </div>
                </button>

                {{-- SSLCommerz --}}
                <button wire:click="selectMethod('sslcommerz')" type="button" class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl border-2 transition-all duration-150
                    {{ $selectedMethod === 'sslcommerz'
                        ? 'border-primary-500 bg-primary-50'
                        : 'border-gray-100 bg-gray-50 hover:border-gray-200 hover:bg-white' }}">

                    {{-- SSLCommerz logo mark --}}
                    <div class="w-14 h-14 rounded-xl bg-[#0B5EA8] flex items-center justify-center shrink-0">
                        <span class="text-white font-black text-[9px] leading-tight text-center">SSL<br>Commerz</span>
                    </div>

                    <div class="text-left flex-1">
                        <p class="text-sm font-semibold text-gray-800">SSLCommerz</p>
                        <p class="text-xs text-gray-400">Card · online bank · mobile bank</p>
                        <div>
                            <img src="{{asset('images/sslcommerz-we-accept.png')}}" />
                        </div>
                    </div>

                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors
                    {{ $selectedMethod === 'sslcommerz' ? 'border-primary-500 bg-primary-500' : 'border-gray-300' }}">
                        @if ($selectedMethod === 'sslcommerz')
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        @endif
                    </div>

                </button>
            </div>

            {{-- Footer --}}
            <div class="px-5 pb-5">
                <button wire:click="proceedPayment" type="button" @disabled(empty($selectedMethod)) class="w-full py-3 rounded-xl font-semibold text-sm transition-all duration-150 flex items-center justify-center gap-2
                    {{ !empty($selectedMethod)
                        ? 'btn-primary'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                    <span wire:loading.remove wire:target="proceedPayment">
                        @if (!empty($selectedMethod))
                        Continue with
                        {{ match($selectedMethod) {
                        'bkash' => 'bKash',
                        'sslcommerz' => 'SSLCommerz',
                        'bank' => 'Bank Transfer',
                        default => ''
                        } }}
                        @else
                        Choose a method
                        @endif
                    </span>
                    <span wire:loading wire:target="proceedPayment" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Redirecting...
                    </span>
                </button>

                <p class="text-center text-xs text-gray-400 mt-3">
                    <svg class="w-3 h-3 inline-block mb-0.5 mr-0.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Secured & encrypted payment
                </p>
            </div>

        </div>
    </div>
    @endif
</div>