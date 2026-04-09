<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <div
        class="md:hidden fixed bottom-0 left-0 right-0 z-[100] bg-white border-t border-gray-200 pb-safe shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
        <div class="flex items-center justify-around h-16 px-2" x-data="{ activeTab: 'products' }">

            <button @click="activeTab = 'home'"
                class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors"
                :class="activeTab === 'home' ? 'text-primary-500' : 'text-gray-400'">
                <x-heroicon-o-home class="w-6 h-6" />
                <span class="text-[10px] font-medium">Home</span>
            </button>

            <button @click="activeTab = 'products'"
                class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors"
                :class="activeTab === 'products' ? 'text-primary-500' : 'text-gray-400'">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="text-[10px] font-medium">Products</span>
            </button>

            <button @click="activeTab = 'order'"
                class="relative flex flex-col items-center justify-center flex-1 -mt-8 gap-1">
                <div class="w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-transform active:scale-90"
                    :class="activeTab === 'order' ? 'bg-primary-500 text-white' : 'bg-white border border-gray-100 text-primary-500'">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold mt-1"
                    :class="activeTab === 'order' ? 'text-primary-500' : 'text-gray-500'">Request</span>
            </button>

            <a href="YOUR_FB_GROUP_LINK" target="_blank"
                class="flex flex-col items-center justify-center flex-1 gap-1 text-gray-400">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
                <span class="text-[10px] font-medium">Group</span>
            </a>

            <a href="https://wa.me/YOUR_NUMBER" target="_blank"
                class="flex flex-col items-center justify-center flex-1 gap-1 text-gray-400">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                </svg>
                <span class="text-[10px] font-medium">WhatsApp</span>
            </a>

        </div>
    </div>

    <div class="h-20 md:hidden"></div>
</div>