<?php

use Livewire\Component;

new class extends Component
{

    public string $productURL = '';

    public function orderRequest()
    {
        $this->redirectRoute('order-request', [
            'product_link' => $this->productURL,
        ]);
    }
};
?>
{{-- Animated border search bar --}}

<div class="flex flex-col gap-2 py-8">

    <style>
        @keyframes borderPulse {

            0%,
            100% {
                border-color: #fe5265;
                /* primary-500 */
            }

            50% {
                border-color: #86868654;
                /* primary-300 */
            }
        }

        .animate-border-pulse {
            animation: borderPulse 2s ease-in-out infinite;
        }
    </style>

    <div class="bg-white border-2 shadow-xl shadow-gray-200/40 rounded-2xl p-2 md:p-3 animate-border-pulse">
        <div class="flex flex-wrap md:flex-nowrap items-center gap-3">
            <div class="flex-1 flex items-center gap-3 px-4">
                <span class="p-2 bg-gray-100 rounded-full">
                    <x-heroicon-o-link class="h-5 w-5 text-primary-500" />
                </span>
                <input type="url" wire:model='productURL'
                    placeholder="Paste a product link from Amazon, Alibaba, AliExpress, eBay..."
                    class="w-full text-sm text-gray-800 placeholder-gray-400 bg-transparent outline-none border-none py-2 focus:ring-0" />
            </div>

            <button wire:click="orderRequest"
                class="w-full md:w-auto shrink-0 text-sm font-semibold text-white bg-primary-500 hover:bg-primary-600 px-10 py-3 rounded-xl transition-all shadow-md active:scale-95">
                Order Request
            </button>
        </div>
    </div>
</div>