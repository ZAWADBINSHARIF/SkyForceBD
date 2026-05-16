<?php

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Traits\RequiresCustomerAuth;
use App\Traits\WithSslCommerz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{

    use RequiresCustomerAuth, WithSslCommerz;

    #[Url]
    public string $status = OrderStatus::OrderRequest->value;

    public bool $showPaymentModal = false;
    public string $selectedMethod = '';

    public ?string $order_id;
    public string $product_name = 'Advance Payment';
    public string $product_category = 'Order lead accepted';
    public string $product_profile = 'non-physical-goods';

    public string $customerName;
    public string $phoneNumber;
    public string $email = 'info@skyforcebd.com';
    public ?string $fullAddress = null;

    public bool $shipping_method = false;

    public string $amount;

    public array $tabs = [
        OrderStatus::OrderRequest->value => [
            'label'     => 'Request',
            'icon'      => 'shopping-cart',
            'attribute' => 'order_status'
        ],

        OrderStatus::Responsed->value => [
            'label'     => 'Responsed',
            'icon'      => 'check-circle',
            'attribute' => 'order_status'
        ],

        DeliveryStatus::Processing->value => [
            'label'     => 'Processing',
            'icon'      => 'cog-6-tooth',
            'attribute' => 'delivery_status'
        ],

        DeliveryStatus::Shipped->value => [
            'label'     => 'Shipped',
            'icon'      => 'truck',
            'attribute' => 'delivery_status'
        ],

        DeliveryStatus::Delivered->value => [
            'label'     => 'Delivered',
            'icon'      => 'check-badge',
            'attribute' => 'delivery_status'
        ],

        DeliveryStatus::Cancelled->value => [
            'label'     => 'Cancelled',
            'icon'      => 'x-circle',
            'attribute' => 'delivery_status'
        ],

        OrderStatus::Rejected->value => [
            'label'     => 'Rejected',
            'icon'      => 'no-symbol',
            'attribute' => 'order_status'
        ],
    ];

    public function mount()
    {
        $this->ensureCustomerAuth();
    }

    #[Computed(persist: true, seconds: 180, cache: true)]
    private function getOrders(): Collection
    {
        $attribute = $this->tabs[$this->status]['attribute'] ?? null;

        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        if ($customer) {
            $orders = $customer->orders()
                ->when($attribute && $this->status, function ($query) use ($attribute) {
                    $query->where($attribute, $this->status);
                })
                ->get();
            return $orders;
        } else {
            return collect();
        }
    }

    public function getStatusCount(string $attribute, string $value): int
    {
        $customerId = auth('customer')->user()?->id;

        return Order::query()
            ->where('customer_id', $customerId)
            ->where($attribute, $value)
            ->count();
    }

    #[On('cancel-order-request')]
    public function rejectTheResponse(string $order_id)
    {
        Order::where('id', $order_id)->update([
            'order_status' => OrderStatus::Rejected->value,
        ]);
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function updatedStatus()
    {
        $this->getOrders();
    }

    public function openTheOrderCancelConfirmationModal(string $order_id): void
    {
        $this->dispatch(
            'open-confirmation-modal',
            title: 'Cancel',
            message: 'Are you sure you want to cancel Order request?',
            callEvent: 'cancel-order-request',
            eventPayload: [$order_id],
            confirmText: 'Yes',
            cancelText: 'No',
            confirmColor: 'red',
        );
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

    public function handlePaymentSslCommerz(): void
    {
        if ($this->order_id === null) {
            return;
        }

        $order = Order::query()->find($this->order_id);

        $this->product_name = 'Advance Payment';
        $this->product_category = 'Order lead accepted';
        $this->product_profile = 'non-physical-goods';

        $this->customerName = $order->customer_name;
        $this->phoneNumber = $order->customer_phone;
        $this->shipping_method = false;

        $this->amount = $order->advance_payment;

        $this->setPostData();
        $this->paymentForAdvance($this->order_id);
    }

    public function proceedPayment(): void
    {
        if (empty($this->selectedMethod)) {
            return;
        }

        // TODO: handle each method
        match ($this->selectedMethod) {
            'bkash'       => $this->redirect(route('payment.bkash')),
            'sslcommerz'  => $this->handlePaymentSslCommerz(),
            'bank'        => $this->redirect(route('payment.bank')),
            default       => null,
        };
    }

    public function render()
    {
        return $this->view([
            'orders' => $this->getOrders(),
        ]);
    }
};
?>

<div class="min-h-screen">

    {{-- Tabs --}}
    <div class="border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 py-5 bg-white rounded-lg my-5">
            <div class="flex justify-center items-center flex-wrap scrollbar-none">
                @foreach ($tabs as $key => $tab)
                <button wire:key="tab-{{ $key }}" wire:click="setStatus('{{ $key }}')" class="relative flex items-center gap-2 px-4 py-3.5 text-xs font-semibold whitespace-nowrap transition-all
                            {{ $status === $key ? 'text-gray-900' : 'text-gray-400 hover:text-gray-600' }}">

                    @svg('heroicon-o-' . $tab['icon'], 'w-3.5 h-3.5')
                    {{ $tab['label'] }}

                    <span @class([ 'text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center'
                        , 'bg-gray-900 text-white'=> $status === $key,
                        'bg-gray-100 text-gray-400' => $status !== $key,
                        ])>
                        {{ $this->getStatusCount($tab['attribute'], $key) }}
                    </span>

                    {{-- Active underline --}}
                    @if ($status === $key)
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gray-900 rounded-full"></span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Orders --}}
    <div class="max-w-6xl mx-auto bg-white rounded-lg px-4 py-5 space-y-3">

        @foreach ($orders as $order)


        <div class="border border-gray-100 rounded-xl overflow-hidden">

            {{-- Order ID bar --}}
            <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                <span class="text-xs font-bold text-gray-500 tracking-widest uppercase font-mono">
                    #{{ $order->order_number_short_code }}
                </span>
                <button class="px-3 py-1 bg-gray-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-md
                                        hover:bg-gray-700 transition-colors">
                    Checkout
                </button>
            </div>

            {{-- Products --}}
            @foreach ($order['products'] as $product)
            <div class="flex gap-3 px-4 py-4 border-b border-gray-50">

                {{-- Info --}}
                <div class="flex-1 min-w-0 flex items-start justify-between gap-3">

                    <div class="flex-1 min-w-0">

                        <p class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">
                            {{ $product['name'] ?? 'Unnamed' }}
                        </p>

                        @if (!empty($product['link']))
                        <a href="{{ $product['link'] }}" target="_blank" rel="noopener noreferrer"
                            class="text-xs text-primary-500 hover:underline break-all line-clamp-2">
                            {{ $product['link'] }}
                        </a>
                        @endif

                    </div>

                    {{-- Price --}}
                    @if (
                    isset($product['unit_price']) &&
                    isset($product['total_price']) &&
                    isset($product['quantity'])
                    )
                    <div class="shrink-0 text-right">
                        <p class="text-xs font-bold text-primary-500 whitespace-nowrap">
                            BDT {{ number_format((float) $product['unit_price']) }}
                            ×
                            {{ number_format((float) $product['quantity']) }}
                            =
                            {{ number_format((float) $product['total_price']) }}
                        </p>
                    </div>
                    @endif

                </div>

            </div>
            @endforeach

            <div class="px-2">
                @if ($order['customer_remark'])
                <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                    Note: {{ $order['customer_remark'] }}
                </p>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-4 py-3 flex items-center justify-end gap-3">
                @if ($order->isOrderRequest())
                <div class="flex flex-col items-end">

                    <p class="text-xs text-gray-500">
                        <span class="font-bold text-primary-500 ml-1">Inquiring...</span>
                    </p>

                </div>
                @else
                <div class="flex flex-col items-end">

                    <p class="text-xs text-gray-500">
                        Shipping charage:
                        <span class="font-bold text-primary-500 ml-1">BDT {{ number_format($order['shipping_charge'])
                            }}</span>
                    </p>
                    <p class="text-xs text-gray-500">
                        Lead Total:
                        <span class="font-bold text-primary-500 ml-1">BDT {{ number_format($order['total_price'])
                            }}</span>
                    <p class="text-xs text-gray-500">
                        Paid:
                        <span class="font-bold text-green-600 ml-1">BDT {{ number_format($order['total_paid'])
                            }}</span>
                    </p>

                </div>
                @endif

                @if ($order->isResponded() && $order->isPending() && $order->advance_payment > 0)

                <button wire:click="openTheOrderCancelConfirmationModal({{$order->id}})"
                    class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                    Cancel
                </button>
                <button
                    wire:click="$set('order_id', {{ $order->id }}); $set('amount', {{ $order->advance_payment }}); openModal()"
                    class="px-3.5 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-xs font-semibold rounded-lg
                    transition-colors">
                    Pay Advance ৳{{$order->advance_payment}}
                </button>
                @elseif ($order->isProcessing())
                <div class="flex items-center gap-1 text-green-600">
                    @svg('heroicon-o-check-circle', 'w-4 h-4')
                    <span class="text-xs font-semibold">Processing</span>
                </div>
                @elseif ($order->isShipped())
                <div class="flex items-center gap-1 text-green-600">
                    @svg('heroicon-o-check-circle', 'w-4 h-4')
                    <span class="text-xs font-semibold">Shipped</span>
                </div>
                @elseif ($order->isDelivered())
                <div class="flex items-center gap-1 text-green-600">
                    @svg('heroicon-o-check-circle', 'w-4 h-4')
                    <span class="text-xs font-semibold">Delivered</span>
                </div>
                @elseif ($order->isCancelled())
                <div class="flex items-center gap-1 text-green-600">
                    @svg('heroicon-o-check-circle', 'w-4 h-4')
                    <span class="text-xs font-semibold">Cancelled</span>
                </div>
                @elseif ($order->isRejected())
                <div class="flex items-center gap-1 text-green-600">
                    @svg('heroicon-o-check-circle', 'w-4 h-4')
                    <span class="text-xs font-semibold">Rejected</span>
                </div>
                @endif
            </div>

        </div>

        @endforeach

        @if($orders->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center mb-3">
                @svg('heroicon-o-clipboard-document-list', 'w-6 h-6 text-gray-300')
            </div>
            <p class="text-sm font-semibold text-gray-600 mb-1">No orders here</p>
            <p class="text-xs text-gray-400 mb-5">Nothing with this status yet.</p>
        </div>
        @endif

    </div>

    {{-- Modal --}}
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
                    <p class="text-xs text-gray-400 mt-0.5">Total: <span
                            class="font-semibold text-gray-700">৳{{$this->amount}}</span>
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

                    <div
                        class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors
                        {{ $selectedMethod === 'sslcommerz' ? 'border-primary-500 bg-primary-500' : 'border-gray-300' }}">
                        @if ($selectedMethod === 'sslcommerz')
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        @endif
                    </div>

                </button>

                {{-- Bank Transfer --}}
                <button wire:click="selectMethod('bank')" type="button" class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl border-2 transition-all duration-150
                                    {{ $selectedMethod === 'bank'
                                        ? 'border-emerald-500 bg-emerald-50'
                                        : 'border-gray-100 bg-gray-50 hover:border-gray-200 hover:bg-white' }}">

                    {{-- Bank icon --}}
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11M8 10v11M12 10v11M16 10v11" />
                        </svg>
                    </div>

                    <div class="text-left flex-1">
                        <p class="text-sm font-semibold text-gray-800">Bank Transfer</p>
                        <p class="text-xs text-gray-400">Direct deposit · 1–2 business days</p>
                    </div>

                    <div
                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors
                                    {{ $selectedMethod === 'bank' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300' }}">
                        @if ($selectedMethod === 'bank')
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