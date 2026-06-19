<?php

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\StoragePath;
use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\Transaction;
use App\Traits\RequiresCustomerAuth;
use App\Traits\WithSslCommerz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new class extends Component
{

    use RequiresCustomerAuth, WithSslCommerz, WithFileUploads;

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

    // ── Bank transfer step ────────────────────────────────────────
    public int    $bankStep           = 1; // 1 = info, 2 = upload
    public ?TemporaryUploadedFile $bankProofImagePath = null;
    public bool   $bankUploading      = false;

    // Sky Force BD bank details
    public array $bankDetails = [
        [
            'bank'    => 'BRAC Bank',
            'name'    => 'Computer Importer',
            'account' => '2066749090001',
            'routing' => null,
            'branch'  => 'Elephant Road,Dhaka',
        ],
        [
            'bank'    => 'City Bank',
            'name'    => 'Computer Importer',
            'account' => '1781910008610',
            'routing' => null,
            'branch'  => "Bhulta Branch,Narayanganj",
        ],
        [
            'bank'    => 'Dutch Bangla Bank',
            'name'    => 'Computer Importer',
            'account' => '1261100043604',
            'routing' => "Elephant Road,Dhaka",
            'branch'  => "090261338",
        ],
    ];

    public function mount()
    {
        $this->ensureCustomerAuth();
    }

    #[Computed(persist: true, seconds: 180, cache: true)]
    private function getOrders(): Collection
    {

        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        if ($customer) {
            $orders = $customer->orders()
                ->with('transactions')
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

    public function closeModal(): void
    {
        $this->showPaymentModal = false;
        $this->selectedMethod   = '';
        $this->bankStep         = 1;         // ← reset bank step
        $this->bankProofImagePath = null;
    }

    public function selectMethod(string $method): void
    {
        $this->selectedMethod = $method;
        $this->bankStep       = 1;           // ← reset when switching method
        $this->bankProofImagePath = null;
    }

    public function goToBankUpload(): void
    {
        $this->bankStep = 2;
    }

    public function goBackToBankInfo(): void
    {
        $this->bankStep = 1;
    }

    public function uploadBankProof(): void
    {
        $this->validate([
            'bankProofImagePath' => ['required', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'bankProofImagePath.required' => 'Please upload a payment screenshot.',
            'bankProofImagePath.mimes'    => 'File must be a JPG, PNG or WebP image.',
            'bankProofImagePath.max'      => 'Image must be under 4MB.',
        ]);

        if ($this->order_id === null) {
            return;
        }

        $order = Order::query()->find($this->order_id);

        if (! $order) {
            return;
        }

        $path = $this->bankProofImagePath->store(
            StoragePath::TransactionProof->value,
            'public'
        );

        Transaction::create([
            'order_id'               => $this->order_id,
            'transaction_number'     => 'BANK-' . strtoupper(uniqid()),
            'payment_method'         => 'bank_transfer',
            'payment_amount'         => $order->advance_payment,
            'status'                 => TransactionStatus::Pending,
            'bank_transaction_image' => $path,
            'payment_info'           => ['note' => 'Pending bank transfer verification'],
        ]);

        $this->closeModal();

        $this->dispatch('notify', message: 'Payment submitted. We will verify and confirm shortly.');
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

    {{-- Orders --}}
    <div class="max-w-6xl mx-auto bg-white rounded-lg px-4 py-5 space-y-3 mt-12">

        @foreach ($orders as $order)


        <div class="border border-gray-100 rounded-xl overflow-hidden">

            {{-- Order ID bar --}}
            <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                <span class="text-xs font-bold text-gray-500 tracking-widest uppercase font-mono">
                    #{{ $order->order_number_short_code }}
                </span>
                <button
                    class="px-3 py-1 bg-gray-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-md hover:bg-gray-700 transition-colors">
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
                    @if (isset($product['unit_price'], $product['total_price'], $product['quantity']))
                    <div class="shrink-0 text-right">
                        <p class="text-xs font-bold text-primary-500 whitespace-nowrap">
                            BDT {{ number_format((float) $product['unit_price']) }}
                            × {{ number_format((float) $product['quantity']) }}
                            = {{ number_format((float) $product['total_price']) }}
                        </p>
                    </div>
                    @endif

                </div>

            </div>
            @endforeach

            {{-- Customer remark --}}
            @if ($order['customer_remark'])
            <div class="px-4 py-2">
                <p class="text-xs text-gray-500 line-clamp-1">
                    Note: {{ $order['customer_remark'] }}
                </p>
            </div>
            @endif

            {{-- ================== SIMPLE STATUS BADGES ================== --}}
            @php
            // Determine the current order status string
            $currentOrderStatus = 'order_request'; // default
            if ($order->isOrderRequest()) {
            $currentOrderStatus = 'order_request';
            } elseif ($order->isResponded()) {
            $currentOrderStatus = 'responsed';
            } elseif ($order->isAccepted()) {
            $currentOrderStatus = 'accepted';
            } elseif ($order->isRejected()) {
            $currentOrderStatus = 'rejected';
            }

            // Determine the current delivery status string
            $currentDeliveryStatus = null;
            if ($order->isPending()) {
            $currentDeliveryStatus = 'pending';
            } elseif ($order->isProcessing()) {
            $currentDeliveryStatus = 'processing';
            } elseif ($order->isShipped()) {
            $currentDeliveryStatus = 'shipped';
            } elseif ($order->isDelivered()) {
            $currentDeliveryStatus = 'delivered';
            } elseif ($order->isCancelled()) {
            $currentDeliveryStatus = 'cancelled';
            }

            // Order badge sequence (excluding rejected, handled separately)
            $orderBadges = [
            ['label' => 'Request', 'value' => 'order_request'],
            ['label' => 'Responsed', 'value' => 'responsed'],
            ['label' => 'Accepted', 'value' => 'accepted'],
            ];

            // Delivery badge sequence
            $deliveryBadges = [
            ['label' => 'Pending', 'value' => 'pending'],
            ['label' => 'Processing', 'value' => 'processing'],
            ['label' => 'Shipped', 'value' => 'shipped'],
            ['label' => 'Delivered', 'value' => 'delivered'],
            ];

            // For logic: find current index in order sequence
            $orderValues = array_column($orderBadges, 'value');
            $currentOrderIndex = array_search($currentOrderStatus, $orderValues);
            $isRejected = ($currentOrderStatus === 'rejected');
            @endphp

            <div class="px-4 py-3 space-y-5 bg-gray-50/50 border-t border-gray-100">

                {{-- Order Status Row --}}
                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-1">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mr-1">Order</span>

                    @foreach ($orderBadges as $badge)
                    @php
                    $badgeIndex = array_search($badge['value'], $orderValues);
                    // Active if matches current status
                    $isActive = ($currentOrderStatus === $badge['value']);
                    // Completed if index is less than current (and we are not rejected)
                    $isPast = ($currentOrderIndex !== false && $badgeIndex < $currentOrderIndex) && !$isRejected; // In
                        rejected case, only show Request as filled (completed) if ($isRejected) {
                        $isActive=($badge['value']==='order_request' ); $isPast=false; } @endphp <span
                        class="inline-flex items-center gap-1">
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase
                                {{ $isActive || $isPast ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-400 border border-gray-200' }}">
                            {{ $badge['label'] }}
                        </span>
                        @if (!$loop->last)
                        <span class="text-gray-300 text-xs">→</span>
                        @endif
                        </span>
                        @endforeach

                        {{-- Rejected pill at the end --}}
                        @if ($isRejected)
                        <span class="text-gray-300 text-xs ml-1">→</span>
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase bg-red-100 text-red-600 border border-red-200">
                            Rejected
                        </span>
                        @endif
                </div>

                {{-- Delivery Status Row (only when order has entered delivery phase) --}}
                @if ($order->isAccepted() || $order->isProcessing() || $order->isShipped() || $order->isDelivered() ||
                $order->isCancelled())
                @php
                $deliveryValues = array_column($deliveryBadges, 'value');
                $currentDeliveryIndex = array_search($currentDeliveryStatus, $deliveryValues);
                $isCancelled = ($currentDeliveryStatus === 'cancelled');
                @endphp

                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-1">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mr-1">Delivery</span>

                    @if (!$isCancelled)
                    @foreach ($deliveryBadges as $badge)
                    @php
                    $badgeIndex = array_search($badge['value'], $deliveryValues);
                    $isActive = ($currentDeliveryStatus === $badge['value']);
                    $isPast = ($currentDeliveryIndex !== false && $badgeIndex < $currentDeliveryIndex); @endphp <span
                        class="inline-flex items-center gap-1">
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase
                                        {{ $isActive || $isPast ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-400 border border-gray-200' }}">
                            {{ $badge['label'] }}
                        </span>
                        @if (!$loop->last)
                        <span class="text-gray-300 text-xs">→</span>
                        @endif
                        </span>
                        @endforeach
                        @else
                        {{-- Cancelled pill --}}
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase bg-red-100 text-red-600 border border-red-200">
                            Cancelled
                        </span>
                        @endif
                </div>
                @endif
            </div>

            {{-- Footer (unchanged) --}}
            <div class="px-4 py-3 flex-col gap-3">
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
                            Shipping charge:
                            <span class="font-bold text-primary-500 ml-1">BDT {{
                                number_format($order['shipping_charge'])
                                }}</span>
                        </p>
                        <p class="text-xs text-gray-500">
                            Lead Total:
                            <span class="font-bold text-primary-500 ml-1">BDT {{ number_format($order['total_price'])
                                }}</span>
                        </p>
                        <p class="text-xs text-gray-500">
                            Paid:
                            <span class="font-bold text-green-600 ml-1">BDT {{ number_format($order['total_paid'])
                                }}</span>
                        </p>
                    </div>
                    @endif
                </div>

                <div class="text-end space-x-3">
                    @if ($order->isResponded() && $order->isPending() && $order->advance_payment > 0 &&
                    !$order->isLatestTransactionStatusPending())
                    <button wire:click="openTheOrderCancelConfirmationModal({{$order->id}})"
                        class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button
                        wire:click="$set('order_id', {{ $order->id }}); $set('amount', {{ $order->advance_payment }}); openModal()"
                        class="px-3.5 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-xs font-semibold rounded-lg transition-colors">
                        Pay Advance ৳{{$order->advance_payment}}
                    </button>
                    @endif
                </div>
            </div>

        </div>

        {{-- old design. --}}
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
                <div>

                </div>
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

                @if ($order->isResponded() && $order->isPending() && $order->advance_payment > 0 &&
                !$order->isLatestTransactionStatusPending())

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
    <div class="fixed inset-0 z-70 flex items-center justify-center sm:p-4" wire:click.self="closeModal">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeModal"></div>

        {{-- Panel --}}
        <div class="relative w-full sm:max-w-xl bg-white sm:rounded-2xl rounded-t-2xl shadow-xl z-10
                    flex flex-col max-h-[92dvh] sm:max-h-[88dvh]">

            {{-- Header — fixed, never scrolls --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Select Payment Method</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Total: <span class="font-semibold text-gray-700">৳{{ $this->amount }}</span>
                    </p>
                </div>
                <button wire:click="closeModal" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Scrollable body --}}
            <div class="overflow-y-auto overscroll-contain flex-1 px-5 py-4 space-y-3">

                {{-- bKash --}}
                <button x-show='false' wire:click="selectMethod('bkash')" type="button" class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl border-2 transition-all duration-150
                    {{ $selectedMethod === 'bkash'
                        ? 'border-pink-500 bg-pink-50'
                        : 'border-gray-100 bg-gray-50 hover:border-gray-200 hover:bg-white' }}">

                    <div class="w-12 h-12 rounded-xl bg-[#E2136E] flex items-center justify-center shrink-0">
                        <span class="text-white font-black text-xs tracking-tight">bKash</span>
                    </div>

                    <div class="text-left flex-1 min-w-0">
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

                    <div class="w-12 h-12 rounded-xl bg-[#0B5EA8] flex items-center justify-center shrink-0">
                        <span class="text-white font-black text-[9px] leading-tight text-center">SSL<br>Commerz</span>
                    </div>

                    <div class="text-left flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800">SSLCommerz</p>
                        <p class="text-xs text-gray-400 mb-1">Card · online bank · mobile bank</p>
                        <img src="{{ asset('images/sslcommerz-we-accept.png') }}" class="h-10 object-contain">
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

                    <div class="w-12 h-12 rounded-xl bg-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11M8 10v11M12 10v11M16 10v11" />
                        </svg>
                    </div>

                    <div class="text-left flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800">Bank Transfer</p>
                        <p class="text-xs text-gray-400">Direct deposit · manual verification</p>
                    </div>

                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors
                        {{ $selectedMethod === 'bank' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300' }}">
                        @if ($selectedMethod === 'bank')
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        @endif
                    </div>
                </button>

                {{-- ── Bank Transfer inline steps ── --}}
                @if ($selectedMethod === 'bank')
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 overflow-hidden">

                    @if ($bankStep === 1)
                    <div class="px-4 py-3">
                        <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest mb-2.5">
                            Transfer to one of these accounts
                        </p>

                        <div class="space-y-2">
                            @foreach ($bankDetails as $bank)
                            <div class="flex-1 bg-white rounded-lg border border-emerald-100 px-3.5 py-2.5">
                                <p class="text-xs font-bold text-gray-800 mb-1.5">{{ $bank['bank'] }}</p>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] text-gray-400 shrink-0">Account name</span>
                                        <span class="text-[10px] font-semibold text-gray-700 text-right">{{
                                            $bank['name']
                                            }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] text-gray-400 shrink-0">Account no.</span>
                                        <span class="text-[10px] font-bold text-emerald-700 font-mono tracking-wide">{{
                                            $bank['account'] }}</span>
                                    </div>
                                    @if ($bank['routing'])
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] text-gray-400 shrink-0">Routing</span>
                                        <span class="text-[10px] font-semibold text-gray-700 font-mono">{{
                                            $bank['routing']
                                            }}</span>
                                    </div>
                                    @endif
                                    @if ($bank['branch'])
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] text-gray-400 shrink-0">Branch</span>
                                        <span class="text-[10px] font-semibold text-gray-700 text-right">{{
                                            $bank['branch']
                                            }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div
                            class="flex items-start gap-2 mt-3 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-[10px] text-amber-700 leading-relaxed">
                                Transfer exactly <strong>৳{{ $this->amount }}</strong> and upload your receipt on the
                                next
                                step.
                                Your order will be confirmed after verification.
                            </p>
                        </div>

                        <button wire:click="goToBankUpload" type="button" class="w-full mt-3 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700
                                   text-white text-xs font-semibold transition-colors">
                            I've Made the Transfer →
                        </button>
                    </div>

                    @elseif ($bankStep === 2)
                    <div class="px-4 py-3">

                        <div class="flex items-center gap-2 mb-3">
                            <button wire:click="goBackToBankInfo" type="button"
                                class="text-emerald-600 hover:text-emerald-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <p class="text-xs font-bold text-emerald-700">Upload Payment Proof</p>
                        </div>

                        <p class="text-[10px] text-gray-500 mb-3 leading-relaxed">
                            Upload a screenshot or photo of your bank transfer receipt. Accepted: JPG, PNG (max 4MB).
                        </p>

                        <div>
                            @if (! $bankProofImagePath)
                            <label for="bank-proof-upload" class="flex flex-col items-center justify-center gap-2 border-2 border-dashed
                                       border-emerald-200 rounded-xl py-8 cursor-pointer
                                       hover:border-emerald-400 hover:bg-white transition-all">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <div class="text-center px-4">
                                    <p class="text-xs font-semibold text-emerald-700">Tap to upload</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG up to 4MB</p>
                                </div>
                                <input id="bank-proof-upload" type="file" class="hidden" accept="image/*"
                                    wire:model="bankProofImagePath">
                            </label>

                            @else
                            <div class="relative rounded-xl overflow-hidden border border-emerald-200">
                                <img src="{{ $bankProofImagePath->temporaryUrl() }}" alt="Payment proof"
                                    class="w-full max-h-48 object-cover">
                                <button wire:click="$set('bankProofImagePath', null)" type="button" class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-500 text-white
                                           flex items-center justify-center hover:bg-red-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            @endif

                            {{-- Upload progress --}}
                            <div wire:loading wire:target="bankProofImagePath"
                                class="flex items-center gap-2 mt-2 text-[10px] text-emerald-600">
                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                Uploading...
                            </div>

                            @error('bankProofImagePath')
                            <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <button wire:click="uploadBankProof" type="button" @disabled($bankProofImagePath===null) class="w-full mt-3 py-2.5 rounded-lg text-xs font-semibold transition-all
                            {{ $bankProofImagePath !== null
                                ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                            <span wire:loading.remove wire:target="uploadBankProof">Submit Payment</span>
                            <span wire:loading wire:target="uploadBankProof"
                                class="flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                Submitting...
                            </span>
                        </button>

                    </div>
                    @endif

                </div>
                @endif

            </div>

            {{-- Footer — fixed, never scrolls --}}
            @if ($selectedMethod !== 'bank')
            <div class="px-5 py-4 border-t border-gray-100 shrink-0">
                <button wire:click="proceedPayment" type="button" @disabled(empty($selectedMethod)) class="w-full py-3 rounded-xl font-semibold text-sm transition-all duration-150
                           flex items-center justify-center gap-2
                    {{ ! empty($selectedMethod)
                        ? 'btn-primary'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                    <span wire:loading.remove wire:target="proceedPayment">
                        @if (! empty($selectedMethod))
                        Continue with
                        {{ match($selectedMethod) {
                        'bkash' => 'bKash',
                        'sslcommerz' => 'SSLCommerz',
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
            @else
            <div class="px-5 py-3 border-t border-gray-100 shrink-0">
                <p class="text-center text-xs text-gray-400">
                    <svg class="w-3 h-3 inline-block mb-0.5 mr-0.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Transfers verified within 1–2 business hours
                </p>
            </div>
            @endif

        </div>
    </div>
    @endif

</div>