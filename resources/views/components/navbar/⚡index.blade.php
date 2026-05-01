<?php

use Livewire\Component;

new class extends Component
{
    public function goTo(string $name)
    {
        return redirect()->route($name);
    }
};
?>

<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="container mx-auto px-4 md:px-6 h-20 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="/" wire:navigate>
                <img width="80" src="{{asset('images/skyforce-logo.png')}}" />
            </a>
        </div>
        <div class="hidden md:flex items-center gap-1">
            <a href="/"
                class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">Home</a>
            <a <a href="/products"
                class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">Products</a>
            <a href="/orders"
                class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z" />
                    <path stroke-linecap="round" d="M16 3v4M8 3v4" />
                </svg>
                Orders
            </a>
            <a
                class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z" />
                </svg>
                Wishlist
            </a>
        </div>

        <div class="flex items-center gap-2">

            <div>
                <button wire:click='goTo("order-request")' class="btn-primary">Order
                    Request</button>
            </div>

            <div class="hidden md:flex items-center gap-2">
                <button wire:click="$dispatch('open-auth-modal', { mode: 'signin' })" class="btn">Sign
                    in</button>
                <button wire:click="$dispatch('open-auth-modal', { mode: 'signup' })" class="btn">Sign
                    up</button>
            </div>
            <div
                class="w-10 h-10 rounded-full bg-[#EEEDFE] flex items-center justify-center text-xs font-medium text-[#3C3489] cursor-pointer"
                wire:click="$dispatch('open-profile-modal')"
                >
                JD</div>
        </div>
    </div>

</nav>