<?php

use App\Models\Product;
use Livewire\Component;

new class extends Component
{
    public function getProducts()
    {
        return Product::latest()->take(10)->get();
    }

    public function orderRequest(string $productURL)
    {
        $this->redirectRoute('order-request', [
            'product_link' => $productURL,
        ]);
    }
};
?>

<div class="pb-5">

    <div class="flex items-baseline justify-between mb-3">
        <h2 class="text-base font-medium text-gray-900">Latest products</h2>
        <a href='/products' class="text-sm text-primary-500 cursor-pointer hover:text-primary-600">
            See all →
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">

        @foreach ($this->getProducts() as $product)

        <div
            class="bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-md hover:border-gray-300 transition-all duration-300 group flex flex-col">

            {{-- Image --}}
            <div class="relative aspect-square w-full overflow-hidden shrink-0" style="background: #f3f3f3">

                @if ($product->product_images[0])
                <img src="{{ $product->product_images[0] }}" alt="{{ $product->product_name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @endif

                {{-- Wishlist (no Alpine, optional Livewire future hook) --}}
                <button
                    class="absolute top-2 right-2 p-1.5 rounded-full bg-white/80 backdrop-blur-sm shadow-sm text-gray-400 hover:text-red-500 hover:bg-white transition-all z-10 active:scale-90"
                    wire:click="toggleWishlist({{ $product->id }})">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>

            </div>

            {{-- Info --}}
            <div class="p-2.5 flex flex-col flex-1">

                <p class="text-xs font-medium text-gray-800 truncate mb-1">
                    {{ $product->product_name }}
                </p>

                <div class="flex items-end justify-between mb-3">

                    <div class="flex flex-col">

                        @if($product->old_price)
                        <span class="text-[10px] text-gray-400 line-through leading-none mb-0.5 flex items-center">
                            <x-fas-bangladeshi-taka-sign class="w-3 h-3" />
                            {{ $product->old_price }}
                        </span>
                        @endif

                        <span class="text-sm font-bold text-[#3C3489] leading-none flex items-center">
                            <x-fas-bangladeshi-taka-sign class="w-3 h-3" />
                            {{ $product->price }}
                        </span>

                    </div>

                </div>

                <div class="flex gap-1.5 mt-auto">

                    <button wire:click="orderRequest('{{ route('product', $product->slug) }}')"
                        class="flex-1 py-1.5 text-xs font-semibold text-white bg-primary-500 hover:bg-primary-600 rounded-lg transition-colors duration-200">
                        Order Request
                    </button>

                    <a href="{{ route('product', $product->slug) }}"
                        class="flex-1 py-1.5 text-xs font-semibold text-center text-primary-500 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors duration-200">
                        Details
                    </a>

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>