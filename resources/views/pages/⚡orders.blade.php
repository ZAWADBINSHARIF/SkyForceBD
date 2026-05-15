<?php

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Traits\RequiresCustomerAuth;
use App\Traits\WithSslCommerz;
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
    private function getOrders()
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
            logger($orders->count());
            return $orders;
        } else {
            return [];
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

    public function render()
    {
        return $this->view([
            'orders' => $this->getOrders(),
            // 'counts' => $counts,
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
                    </p>

                </div>
                @endif

                @if ($order->isResponded() && $order->isPending() && $order->advance_payment > 0)

                <button
                    wire:click="openTheOrderCancelConfirmationModal({{$order->id}})"
                    class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                    Cancel
                </button>
                <button
                    class="px-3.5 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-xs font-semibold rounded-lg transition-colors">
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

        @empty($orders->toArray())
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center mb-3">
                @svg('heroicon-o-clipboard-document-list', 'w-6 h-6 text-gray-300')
            </div>
            <p class="text-sm font-semibold text-gray-600 mb-1">No orders here</p>
            <p class="text-xs text-gray-400 mb-5">Nothing with this status yet.</p>
        </div>
        @endempty


    </div>
</div>