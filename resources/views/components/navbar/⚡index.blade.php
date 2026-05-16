<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{

    public ?Customer $customer = null;

    public function mount()
    {
        $this->customer = Auth::guard('customer')->user();
    }

    public function goTo(string $name)
    {
        return redirect()->route($name);
    }

    #[On('sign-out')]
    public function signOut(): void
    {
        Auth::guard('customer')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect('/');
    }

    public function openTheSignOutConfirmationModal(): void
    {
        $this->dispatch(
            'open-confirmation-modal',
            title: 'Sign out',
            message: 'Are you sure you want to sign out?',
            callEvent: 'sign-out',
            confirmText: 'Yes',
            cancelText: 'No',
            confirmColor: 'red',
        );
    }
};
?>

<nav class="bg-white border-b border-gray-200 sticky top-0 z-60">
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

            @if (Auth::guard('customer')->check())
            <a href="/orders"
                class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z" />
                    <path stroke-linecap="round" d="M16 3v4M8 3v4" />
                </svg>
                Orders
            </a>
            @endif

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

            <div class="hidden md:flex">
                <button wire:click='goTo("order-request")' class="btn-primary">Order
                    Request</button>
            </div>

            @if ($customer)
            <div class="items-center gap-2">
                <button wire:click="goTo('orders')" class="btn md:hidden">
                    Orders
                </button>
                <button wire:click="openTheSignOutConfirmationModal" class="btn">
                    Sign Out
                </button>
            </div>
            @else
            <div class="flex items-center gap-2">
                <button wire:click="$dispatch('open-auth-modal', { mode: 'signin' })" class="btn">Sign
                    in</button>
                <button wire:click="$dispatch('open-auth-modal', { mode: 'signup' })" class="btn">Sign
                    up</button>
            </div>
            @endif


            @if($customer)
            <div class="w-10 h-10 rounded-full bg-[#EEEDFE] flex items-center justify-center text-xs font-medium text-[#3C3489] cursor-pointer"
                wire:click="$dispatch('open-profile-modal')">{{
                strtoupper(substr($customer->full_name, 0, 1)) }}</div>
            @endif

        </div>
    </div>

</nav>