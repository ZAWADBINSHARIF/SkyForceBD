<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="pb-12" x-data="{
    getVideoData(url) {
        let id = '';

        let thumb = '';
        let link = url;

        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            type = 'youtube';
            id = url.match(/(?:youtu\.be\/|youtube\.com\/(?:.*v=|\/v\/|embed\/))([^?& ]+)/)[1];
            thumb = `https://img.youtube.com/vi/${id}/maxresdefault.jpg`;
        } else if (url.includes('facebook.com') || url.includes('fb.watch')) {
            type = 'facebook';
            thumb = 'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=500&q=80';
        }

        return { id, type, thumb, link };
    },

    videos: [
        { url: 'https://www.youtube.com/watch?v=wuqvrEmreFU', title: 'How to request a custom product link' },
        { url: 'https://www.youtube.com/watch?v=0WByFW1MvNc', title: 'Tracking your international shipments' },
        { url: 'https://www.youtube.com/watch?v=8kG1-un2q5k', title: 'Tracking your international shipments' },
        { url: 'https://www.facebook.com/reel/4289939724608774', title: 'Our Facebook Community Shopping Guide' }
    ],

    scroll(dir) {
        const el = this.$refs.track;
        const amount = el.clientWidth * 0.75;
        el.scrollBy({ left: dir === 'right' ? amount : -amount, behavior: 'smooth' });
    },

    canScrollLeft: false,
    canScrollRight: true,

    updateScroll() {
        const el = this.$refs.track;
        this.canScrollLeft = el.scrollLeft > 0;
        this.canScrollRight = el.scrollLeft + el.clientWidth < el.scrollWidth - 1;
    }
}">
    <div class="relative">

        <!-- Left button -->
        <button x-show="canScrollLeft" @click="scroll('left')"
            class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Right button -->
        <button x-show="canScrollRight" @click="scroll('right')"
            class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Scrollable track -->
        <div x-ref="track" @scroll="updateScroll()" x-init="$nextTick(() => updateScroll())"
            class="flex gap-6 overflow-x-auto scroll-smooth pb-2"
            style="scrollbar-width: none; -ms-overflow-style: none;" onscroll="this.dispatchEvent(new Event('scroll'))">

            <template x-for="v in videos" :key="v.url">
                <div x-data="{ info: getVideoData(v.url) }" @click="window.open(info.link, '_blank')"
                    class="group cursor-pointer flex-none w-72">

                    <div
                        class="relative aspect-video rounded-2xl overflow-hidden bg-gray-200 shadow-sm border border-gray-100">
                        <img :src="info.thumb" :alt="v.title"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

                        <div
                            class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                            <div
                                class="w-12 h-12 rounded-full bg-white/90 shadow-xl flex items-center justify-center transform transition-transform duration-300 group-hover:scale-110">
                                <svg class="w-6 h-6 text-primary-500 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </div>
</div>

<style>
    [x-ref="track"]::-webkit-scrollbar {
        display: none;
    }
</style>