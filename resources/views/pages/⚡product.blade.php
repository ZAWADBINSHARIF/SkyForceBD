<?php

use Livewire\Component;

new class extends Component
{

    public $product, $relatedProducts;

    public function mount(): void
    {
        // Dummy product
        $this->product = (object)[
            'name'        => 'Premium Wireless Noise-Cancelling Headphones',
            'price'       => 8500,
            'old_price'   => 12000,
            'description' => '
                <p>Experience next-level sound with our <strong>Premium Wireless Headphones</strong>. Designed for audiophiles and everyday listeners alike, these headphones deliver crystal-clear audio with deep bass and crisp highs.</p>
                <br>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Active Noise Cancellation (ANC) technology</li>
                    <li>Up to 30 hours battery life</li>
                    <li>Bluetooth 5.2 with 10m range</li>
                    <li>Foldable, lightweight design — only 250g</li>
                    <li>Built-in microphone for hands-free calls</li>
                    <li>Compatible with iOS, Android & PC</li>
                </ul>
                <br>
                <p>Available in Black, White, and Midnight Blue. Comes with a premium carry case and USB-C charging cable.</p>
            ',
            'category' => (object)[
                'name' => 'Electronics',
                'slug' => 'electronics',
            ],
            'images' => collect([
                (object)['url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80'],
                (object)['url' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=600&q=80'],
                (object)['url' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&q=80'],
                (object)['url' => 'https://images.unsplash.com/photo-1577174881658-0f30ed549adc?w=600&q=80'],
            ]),
        ];

        // Dummy related products
        $this->relatedProducts = collect([
            (object)[
                'name'      => 'Wireless Earbuds Pro',
                'price'     => 4500,
                'old_price' => 6000,
                'slug'      => 'wireless-earbuds-pro',
                'images'    => collect([(object)['url' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=300&q=70']]),
            ],
            (object)[
                'name'      => 'Smart Watch Series 5',
                'price'     => 14200,
                'old_price' => 18000,
                'slug'      => 'smart-watch-series-5',
                'images'    => collect([(object)['url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&q=70']]),
            ],
            (object)[
                'name'      => 'Bluetooth Speaker',
                'price'     => 6600,
                'old_price' => 8500,
                'slug'      => 'bluetooth-speaker',
                'images'    => collect([(object)['url' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&q=70']]),
            ],
            (object)[
                'name'      => 'Mechanical Keyboard',
                'price'     => 12100,
                'old_price' => 15000,
                'slug'      => 'mechanical-keyboard',
                'images'    => collect([(object)['url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=300&q=70']]),
            ],
            (object)[
                'name'      => 'Wireless Charger Pad',
                'price'     => 4400,
                'old_price' => 5800,
                'slug'      => 'wireless-charger-pad',
                'images'    => collect([(object)['url' => 'https://images.unsplash.com/photo-1618053448492-2b629c2c912f?w=300&q=70']]),
            ],
        ]);
    }
};
?>

<div class="bg-gray-50 min-h-screen py-10 px-4 md:px-8">
    <div class="max-w-7xl mx-auto">
 
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-primary-500 transition-colors">Home</a>
            <span>/</span>
            <a href="#" class="hover:text-primary-500 transition-colors">{{ $product->category->name }}</a>
            <span>/</span>
            <span class="text-gray-600 truncate max-w-xs">{{ $product->name }}</span>
        </nav>
 
        {{-- Product Top Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">
 
            {{-- Left: Image Gallery --}}
            <div class="flex flex-col gap-3" x-data="{ active: 0 }">
 
                {{-- Main Image --}}
                <div class="aspect-square w-full rounded-2xl overflow-hidden border border-gray-100 bg-white">
                    <template x-for="(img, i) in {{ json_encode($product->images->pluck('url')) }}" :key="i">
                        <img
                            :src="img"
                            :alt="'Product image ' + (i + 1)"
                            class="w-full h-full object-cover transition-opacity duration-200"
                            x-show="active === i"
                        >
                    </template>
                </div>
 
                {{-- Thumbnails --}}
                <div class="flex gap-2 flex-wrap">
                    @foreach($product->images as $i => $image)
                        <button
                            @click="active = {{ $i }}"
                            :class="active === {{ $i }} ? 'border-primary-500 ring-2 ring-primary-200' : 'border-gray-200 hover:border-gray-300'"
                            class="w-16 h-16 rounded-xl overflow-hidden border-2 transition-all duration-150 shrink-0"
                        >
                            <img src="{{ $image->url }}" alt="Thumb {{ $i + 1 }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
 
            </div>
 
            {{-- Right: Product Info --}}
            <div class="flex flex-col gap-5">
 
                {{-- Category Badge --}}
                <div>
                    <a href="#"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-primary-500 bg-primary-50 px-3 py-1 rounded-full hover:bg-primary-100 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A2 2 0 013 9.172V5a2 2 0 012-2z"/>
                        </svg>
                        {{ $product->category->name }}
                    </a>
                </div>
 
                {{-- Product Name + Wishlist --}}
                <div class="flex items-start justify-between gap-4">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>
 
                    {{-- Wishlist Button --}}
                    <button
                        x-data="{ wished: false }"
                        @click="wished = !wished"
                        class="shrink-0 w-11 h-11 rounded-xl border-2 flex items-center justify-center transition-all duration-150"
                        :class="wished ? 'border-primary-300 bg-primary-50' : 'border-gray-200 bg-white hover:border-primary-200'"
                        title="Add to Wishlist"
                    >
                        <svg
                            class="w-5 h-5 transition-all duration-150"
                            :class="wished ? 'fill-primary-500 stroke-primary-500' : 'fill-none stroke-gray-400'"
                            stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                </div>
 
                {{-- Prices --}}
                <div class="flex items-end gap-3">
                    <span class="text-3xl font-bold text-primary-500">৳{{ number_format($product->price) }}</span>
                    @if($product->old_price)
                        <span class="text-lg text-gray-400 line-through mb-0.5">৳{{ number_format($product->old_price) }}</span>
                        <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-full mb-1">
                            -{{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}%
                        </span>
                    @endif
                </div>
 
                {{-- Divider --}}
                <div class="border-t border-gray-100"></div>
 
                {{-- Request Button --}}
                <form action="#" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-primary-500 hover:bg-primary-600 active:scale-95 text-white font-bold text-sm py-3.5 rounded-xl flex items-center justify-center gap-2 transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Request This Product
                    </button>
                </form>
 
                {{-- We Accept --}}
                <div class="flex flex-col gap-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">We Accept</p>
                    <div class="flex flex-wrap gap-2">
                        <img 
                        class="w-89"
                        src="{{asset('images/sslcommerz-we-accept.png')}}" alt="We accept payment info" />
                    </div>
                </div>
 
            </div>
 
        </div>
 
        {{-- Product Description --}}
        <div class="bg-white rounded-2xl border border-gray-100 px-8 py-8 mb-16">
            <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-1 h-5 bg-primary-500 rounded-full inline-block"></span>
                Product Description
            </h2>
            <div class="prose prose-sm prose-gray max-w-none text-gray-600 leading-relaxed">
                {!! $product->description !!}
            </div>
        </div>
 
        {{-- Related Products --}}
        @if($relatedProducts->count())
        <div>
            <div class="flex items-baseline justify-between mb-5">
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-1 h-5 bg-primary-500 rounded-full inline-block"></span>
                    Related Products
                </h2>
                <a href="#" class="text-sm font-medium text-primary-500 hover:text-primary-700 transition-colors">
                    See all →
                </a>
            </div>
 
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($relatedProducts as $related)
                <a href="#"
                    class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 hover:shadow-md transition-all duration-200 group">
 
                    <div class="aspect-square w-full overflow-hidden bg-gray-50">
                        <img
                            src="{{ $related->images->first()?->url }}"
                            alt="{{ $related->name }}"
                            loading="lazy"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                    </div>
 
                    <div class="p-2.5">
                        <p class="text-xs font-semibold text-gray-800 truncate mb-1">{{ $related->name }}</p>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-sm font-bold text-primary-500">৳{{ number_format($related->price) }}</span>
                            @if($related->old_price)
                                <span class="text-[11px] text-gray-400 line-through">৳{{ number_format($related->old_price) }}</span>
                            @endif
                        </div>
                    </div>
 
                </a>
                @endforeach
            </div>
        </div>
        @endif
 
    </div>
</div>