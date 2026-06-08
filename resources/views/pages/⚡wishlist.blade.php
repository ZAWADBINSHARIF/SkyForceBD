<?php

use Livewire\Component;
use App\Models\Customer;
use App\Models\Wishlist;
use App\Traits\RequiresCustomerAuth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination, RequiresCustomerAuth;

    public function mount()
    {
        $this->ensureCustomerAuth();
    }

    public function removeFromWishlist(int $productId): void
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        if (! $customer) return;

        $customer->removeFromWishlist($productId);
    }

    public function render()
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $items = $customer
            ?->wishlistProducts()
            ->with('category')
            ->latest('wishlists.created_at')
            ->paginate(12);

        return $this->view([
            'items' => $items ?? collect(),
        ]);
    }
};
?>

<div class="min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">My Wishlist</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ $items->count() }} saved item(s)</p>
            </div>
        </div>

        @if ($items->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-gray-600 mb-1">Your wishlist is empty</p>
            <p class="text-xs text-gray-400 mb-6">Save items you love and come back to them later.</p>
            <a href="/" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold">
                Browse Products
            </a>
        </div>

        @else

        {{-- Product Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($items as $product)
            <div wire:key="wishlist-{{ $product->id }}"
                class="bg-white border border-gray-100 rounded-2xl overflow-hidden group hover:shadow-md hover:border-gray-200 transition-all duration-200">

                {{-- Image --}}
                <div class="relative aspect-square bg-gray-50 overflow-hidden">
                    @if (! empty($product->product_images[0] ?? null))
                    <img src="{{ Storage::url($product->product_images[0]) }}" alt="{{ $product->product_name }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909" />
                        </svg>
                    </div>
                    @endif

                    {{-- Remove button --}}
                    <button wire:click="removeFromWishlist({{ $product->id }})" wire:loading.attr="disabled"
                        wire:target="removeFromWishlist({{ $product->id }})" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white shadow-sm border border-gray-100
                               flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50
                               transition-colors opacity-0 group-hover:opacity-100">
                        <svg class="w-4 h-4" wire:loading.remove wire:target="removeFromWishlist({{ $product->id }})"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                        </svg>
                        <svg class="w-3 h-3 animate-spin hidden" wire:loading
                            wire:target="removeFromWishlist({{ $product->id }})" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                    </button>
                </div>

                {{-- Info --}}
                <div class="p-3">
                    @if ($product->category)
                    <p class="text-[10px] font-semibold text-primary-500 uppercase tracking-wide mb-0.5">
                        {{ $product->category->name }}
                    </p>
                    @endif

                    <p class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug">
                        {{ $product->product_name }}
                    </p>

                    <a href="/product/{{ $product->slug }}" class="mt-2.5 w-full flex items-center justify-center gap-1.5 py-2 rounded-xl
                               border border-gray-200 text-xs font-semibold text-gray-700
                               hover:bg-gray-50 transition-colors">
                        View Product
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($items->hasPages())
        <div
            class="mt-8 **:text-gray-700! **:bg-transparent! [&_[aria-current=page]_span]:bg-primary-500! [&_[aria-current=page]_span]:text-white! [&_[aria-current=page]_span]:border-primary-500!">
            {{ $items->links() }}
        </div>
        @endif

        @endif

    </div>
</div>