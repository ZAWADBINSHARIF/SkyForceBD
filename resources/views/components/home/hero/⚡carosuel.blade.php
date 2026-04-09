<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="md:col-span-4 rounded-2xl overflow-hidden relative bg-gray-200 h-48 md:h-[450px]" x-data="{
            current: 0,
            slides: [
              'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=900&q=80',
              'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900&q=80',
              'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=900&q=80'
            ],
            autoplay: null,
            init() {
              this.autoplay = setInterval(() => { this.current = (this.current + 1) % this.slides.length }, 3500)
            }
          }">
    <template x-for="(s, i) in slides" :key="i">
        <img :src="s" :class="current === i ? 'opacity-100' : 'opacity-0'"
            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700" :alt="'Slide ' + (i+1)">
    </template>

    <button @click="current = (current - 1 + slides.length) % slides.length"
        class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white flex items-center justify-center transition-colors">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="current = (current + 1) % slides.length"
        class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white flex items-center justify-center transition-colors">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
        <template x-for="(s, i) in slides" :key="i">
            <button @click="current = i" :class="current === i ? 'bg-white w-5' : 'bg-white/50 w-2'"
                class="h-2 rounded-full transition-all duration-300"></button>
        </template>
    </div>
</div>