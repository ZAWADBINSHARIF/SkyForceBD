<?php

use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    #[Url]
    public string $status = 'processing';

    public array $tabs = [
        'processing' => ['label' => 'Processing', 'icon' => 'shopping-cart'],
        'requested'  => ['label' => 'Orders',     'icon' => 'paper-airplane'],
        'transit'    => ['label' => 'In transit',  'icon' => 'paper-airplane'],
        'received'   => ['label' => 'Received',    'icon' => 'shopping-cart'],
        'cancelled'  => ['label' => 'Cancelled',   'icon' => 'x-circle'],
    ];

    private function dummyOrders(): array
    {
        return [
            [
                'id'             => 'LD_59643',
                'status'         => 'processing',
                'customer_name'  => 'Rafiul Islam',
                'phone'          => '+880 1712-345678',
                'origin'         => 'New York',
                'destination'    => 'Dhaka',
                'service_charge' => 15,
                'products' => [
                    [
                        'image'    => 'https://m.media-amazon.com/images/I/81f9bNVIUGL._AC_SL1500_.jpg',
                        'name'     => 'Marvel Legends Series Daredevil: Born Again Daredevil & Jessica Jones, 2 Collectible 6-Inch Action Figures',
                        'note'     => 'Can you tell me the price of 245 toys. With and without tex.',
                        'price'    => 7000,
                        'quantity' => 245,
                    ],
                ],
            ],
            [
                'id'             => 'LD_59200',
                'status'         => 'processing',
                'customer_name'  => 'Nusrat Jahan',
                'phone'          => '+880 1815-987654',
                'origin'         => 'Dubai',
                'destination'    => 'Dhaka',
                'service_charge' => 15,
                'products' => [
                    [
                        'image'    => 'https://m.media-amazon.com/images/I/71zny7BTRlL._AC_SL1500_.jpg',
                        'name'     => 'Apple AirPods Pro (2nd Generation) Wireless Earbuds',
                        'note'     => 'Please include original box.',
                        'price'    => 32000,
                        'quantity' => 2,
                    ],
                ],
            ],
            [
                'id'             => 'LD_58991',
                'status'         => 'requested',
                'customer_name'  => 'Mehedi Hasan',
                'phone'          => '+880 1911-223344',
                'origin'         => 'China',
                'destination'    => 'Dhaka',
                'service_charge' => 10,
                'products' => [
                    [
                        'image'    => 'https://m.media-amazon.com/images/I/61SUj2aKoEL._AC_SL1500_.jpg',
                        'name'     => 'Xiaomi Redmi Note 13 Pro 5G Smartphone 256GB',
                        'note'     => 'Color: Midnight Black. No scratches please.',
                        'price'    => 28000,
                        'quantity' => 3,
                    ],
                ],
            ],
            [
                'id'             => 'LD_58700',
                'status'         => 'transit',
                'customer_name'  => 'Tasnim Akter',
                'phone'          => '+880 1623-556677',
                'origin'         => 'London',
                'destination'    => 'Dhaka',
                'service_charge' => 12,
                'products' => [
                    [
                        'image'    => 'https://m.media-amazon.com/images/I/61CGHv6kmWL._AC_SL1500_.jpg',
                        'name'     => "Nike Air Max 270 Running Shoes Men's Size 10",
                        'note'     => 'Size UK 9, White/Black colorway.',
                        'price'    => 12000,
                        'quantity' => 1,
                    ],
                ],
            ],
            [
                'id'             => 'LD_58300',
                'status'         => 'received',
                'customer_name'  => 'Sabbir Ahmed',
                'phone'          => '+880 1534-112233',
                'origin'         => 'USA',
                'destination'    => 'Dhaka',
                'service_charge' => 15,
                'products' => [
                    [
                        'image'    => 'https://m.media-amazon.com/images/I/71Swqqe7XAL._AC_SL1500_.jpg',
                        'name'     => 'Sony WH-1000XM5 Wireless Noise Canceling Headphones',
                        'note'     => 'Gift wrap if possible.',
                        'price'    => 38000,
                        'quantity' => 1,
                    ],
                ],
            ],
            [
                'id'             => 'LD_57900',
                'status'         => 'cancelled',
                'customer_name'  => 'Rafiul Islam',
                'phone'          => '+880 1712-345678',
                'origin'         => 'Japan',
                'destination'    => 'Dhaka',
                'service_charge' => 10,
                'products' => [
                    [
                        'image'    => 'https://m.media-amazon.com/images/I/71175T9TiKL._AC_SL1500_.jpg',
                        'name'     => 'Canon EOS R50 Mirrorless Camera with 18-45mm Lens Kit',
                        'note'     => 'Wrong product link was submitted.',
                        'price'    => 75000,
                        'quantity' => 1,
                    ],
                ],
            ],
        ];
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function render()
    {
        $all = $this->dummyOrders();

        $filtered = array_values(array_filter($all, fn($o) => $o['status'] === $this->status));

        $counts = [];
        foreach (array_keys($this->tabs) as $key) {
            $counts[$key] = count(array_filter($all, fn($o) => $o['status'] === $key));
        }

        return $this->view([
            'orders' => $filtered,
            'counts' => $counts,
        ]);
    }
};
?>

<div class="min-h-screen">

    {{-- Tabs --}}
    <div class="border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 py-5 bg-white rounded-lg my-5">
            <div class="flex justify-center items-center flex-wrap scrollbar-none">
                @foreach ($tabs as $key => $tab)
                <button wire:click="setStatus('{{ $key }}')" class="relative flex items-center gap-2 px-4 py-3.5 text-xs font-semibold whitespace-nowrap transition-all
                            {{ $status === $key ? 'text-gray-900' : 'text-gray-400 hover:text-gray-600' }}">

                    @svg('heroicon-o-' . $tab['icon'], 'w-3.5 h-3.5')
                    {{ $tab['label'] }}

                    @if (!empty($counts[$key]))
                    <span @class([ 'text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center'
                        , 'bg-gray-900 text-white'=> $status === $key,
                        'bg-gray-100 text-gray-400' => $status !== $key,
                        ])>{{ $counts[$key] }}</span>
                    @endif

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
    <div class="max-w-4xl mx-auto bg-white rounded-lg px-4 py-5 space-y-3">

        @forelse ($orders as $order)
        @php
        $subtotal = array_sum(array_map(fn($p) => $p['price'] * $p['quantity'], $order['products']));
        $serviceAmount = round($subtotal * $order['service_charge'] / 100);
        $leadTotal = $subtotal + $serviceAmount;
        @endphp

        <div class="border border-gray-100 rounded-xl overflow-hidden">

            {{-- Order ID bar --}}
            <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                <span class="text-xs font-bold text-gray-500 tracking-widest uppercase font-mono">
                    {{ $order['id'] }}
                </span>
                <button class="px-3 py-1 bg-gray-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-md
                        hover:bg-gray-700 transition-colors">
                    Checkout
                </button>
            </div>

            {{-- Products --}}
            @foreach ($order['products'] as $product)
            <div class="flex gap-3 px-4 py-4 border-b border-gray-50">

                {{-- Image --}}
                <div class="w-16 h-16 shrink-0 rounded-lg border border-gray-100 overflow-hidden bg-gray-50">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover"
                        onerror="this.src='https://placehold.co/64x64/f9fafb/d1d5db?text=?'" />
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">
                            {{ $product['name'] }}
                        </p>
                        @if ($product['note'])
                        <p class="text-xs text-gray-400 mt-1 line-clamp-1">
                            {{ $product['note'] }}
                        </p>
                        @endif
                        <div class="flex items-center gap-1.5 mt-2">
                            <span class="text-xs font-semibold text-gray-500 italic">{{ $order['origin'] }}</span>
                            @svg('heroicon-o-arrows-right-left', 'w-3 h-3 text-gray-300')
                            <span class="text-xs font-semibold text-gray-500 italic">{{ $order['destination'] }}</span>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="shrink-0 text-right">
                        <p class="text-xs font-bold text-primary-500 whitespace-nowrap">
                            BDT {{ number_format($product['price']) }} × {{ number_format($product['quantity']) }}
                        </p>
                        <p class="text-[10px] text-gray-400 mt-0.5">
                            +{{ $order['service_charge'] }}% service
                        </p>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Footer --}}
            <div class="px-4 py-3 flex items-center justify-end gap-3">
                <p class="text-xs text-gray-500">
                    Lead Total:
                    <span class="font-bold text-primary-500 ml-1">BDT {{ number_format($leadTotal) }}</span>
                </p>

                @if ($order['status'] !== 'cancelled' && $order['status'] !== 'received')
                <button
                    class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                    Cancel
                </button>
                <button
                    class="px-3.5 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-xs font-semibold rounded-lg transition-colors">
                    Pay Now
                </button>
                @elseif ($order['status'] === 'received')
                <div class="flex items-center gap-1 text-green-600">
                    @svg('heroicon-o-check-circle', 'w-4 h-4')
                    <span class="text-xs font-semibold">Delivered</span>
                </div>
                @elseif ($order['status'] === 'cancelled')
                <div class="flex items-center gap-1 text-red-400">
                    @svg('heroicon-o-x-circle', 'w-4 h-4')
                    <span class="text-xs font-semibold">Cancelled</span>
                </div>
                @endif
            </div>

        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center mb-3">
                @svg('heroicon-o-clipboard-document-list', 'w-6 h-6 text-gray-300')
            </div>
            <p class="text-sm font-semibold text-gray-600 mb-1">No orders here</p>
            <p class="text-xs text-gray-400 mb-5">Nothing with this status yet.</p>
            <a href="{{ route('order') }}" wire:navigate
                class="btn-primary px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5">
                @svg('heroicon-o-plus', 'w-3.5 h-3.5')
                New Order
            </a>
        </div>
        @endforelse

    </div>
</div>