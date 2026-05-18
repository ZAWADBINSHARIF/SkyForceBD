<?php

use App\Models\Banner;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

new class extends Component
{

    public Collection $banner;
    public array $slides = [];

    public function mount(): void
    {
        $this->banner = Banner::orderBy('sort_order', 'asc')
            ->get()
            ->slice(0, -1)
            ->values();

        // IMPORTANT: store only relative path (NOT asset())
        $this->slides = $this->banner->map(fn($b) => [
            'image' => $b->image, // storage path
            'link'  => $b->link,
        ])->toArray();
    }
};
?>

<div x-show="@js($banner->isNotEmpty())"
    class="md:col-span-4 rounded-2xl overflow-hidden relative bg-gray-200" x-data="{
        current: 0,
        slides: @js($slides),
        autoplay: null,

        init() {
            if (!this.slides.length) return;

            this.autoplay = setInterval(() => {
                this.current = (this.current + 1) % this.slides.length
            }, 3500)
        }
    }">
    <!-- Slides -->
    <div class="relative w-full aspect-13/7 overflow-hidden">
        <template x-for="(s, i) in slides" :key="i">
            <a :href="s.link">
                <img :src="`/storage/${s.image}`" :class="current === i ? 'opacity-100' : 'opacity-0'"
                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
                    :alt="'Slide ' + (i + 1)" />
            </a>
        </template>
    </div>

    <!-- Prev Button -->
    <button @click="current = (current - 1 + slides.length) % slides.length"
        class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white flex items-center justify-center transition-colors">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <!-- Next Button -->
    <button @click="current = (current + 1) % slides.length"
        class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white flex items-center justify-center transition-colors">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Dots -->
    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
        <template x-for="(s, i) in slides" :key="i">
            <button @click="current = i" :class="current === i ? 'bg-white w-5' : 'bg-white/50 w-2'"
                class="h-2 rounded-full transition-all duration-300"></button>
        </template>
    </div>
</div>