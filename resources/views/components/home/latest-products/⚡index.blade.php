<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="pb-5">
    <div class="flex items-baseline justify-between mb-3">
        <h2 class="text-base font-medium text-gray-900">Latest products</h2>
        <a class="text-sm text-primary-500 cursor-pointer hover:text-primary-600">See all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3" x-data="{
          products: [
            {name:'Wireless earbuds', price:'৳4,999', oldPrice:'৳6,500', badge:'New', color:'#EEEDFE', img:'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=300&q=70'},
            {name:'Running shoes', price:'৳8,900', oldPrice:'৳10,500', badge:'In stock', color:'#E1F5EE', img:'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&q=70'},
            {name:'Smart watch', price:'৳12,900', oldPrice:'৳15,000', badge:'New', color:'#FAEEDA', img:'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&q=70'},
            {name:'Game controller', price:'৳6,550', oldPrice:'৳7,200', badge:'In stock', color:'#FAECE7', img:'https://images.unsplash.com/photo-1486401899868-0e435ed85128?w=300&q=70'},
            {name:'Mechanical keyboard', price:'৳11,000', oldPrice:'৳13,500', badge:'New', color:'#E6F1FB', img:'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=300&q=70'},
            {name:'Desk lamp', price:'৳3,499', oldPrice:'৳4,000', badge:'In stock', color:'#EAF3DE', img:'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=300&q=70'},
            {name:'Sunglasses', price:'৳5,500', oldPrice:'৳7,000', badge:'New', color:'#FBEAF0', img:'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=300&q=70'},
            {name:'Backpack', price:'৳7,900', oldPrice:'৳9,500', badge:'In stock', color:'#F1EFE8', img:'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=300&q=70'},
            {name:'Yoga mat', price:'৳2,999', oldPrice:'৳3,500', badge:'In stock', color:'#E1F5EE', img:'https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=300&q=70'},
            {name:'Coffee mug', price:'৳1,800', oldPrice:'৳2,200', badge:'New', color:'#FAEEDA', img:'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=300&q=70'}
          ]
        }">
        <template x-for="(p, i) in products" :key="i">
            <div x-data="{ isWished: false }"
                class="bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-md hover:border-gray-300 transition-all duration-300 group flex flex-col">

                <div class="relative aspect-square w-full overflow-hidden shrink-0" :style="'background:' + p.color">
                    <img :src="p.img" :alt="p.name"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                    <button @click.prevent="isWished = !isWished"
                        class="absolute top-2 right-2 p-1.5 rounded-full bg-white/80 backdrop-blur-sm shadow-sm text-gray-400 hover:text-red-500 hover:bg-white transition-all z-10 active:scale-90">
                        <svg x-show="!isWished" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <svg x-show="isWished" x-cloak class="w-4 h-4 text-red-500" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="p-2.5 flex flex-col flex-1">
                    <p class="text-xs font-medium text-gray-800 truncate mb-1" x-text="p.name"></p>

                    <div class="flex items-end justify-between mb-3">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 line-through leading-none mb-0.5"
                                x-text="p.oldPrice"></span>
                            <span class="text-sm font-bold text-[#3C3489] leading-none" x-text="p.price"></span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded-full font-medium"
                            :class="p.badge === 'New' ? 'bg-[#EEEDFE] text-[#3C3489]' : 'bg-[#EAF3DE] text-[#27500A]'"
                            x-text="p.badge"></span>
                    </div>

                    <div class="mt-auto pt-1">
                        <button
                            class="w-full py-1.5 text-xs font-semibold text-white bg-primary-500 hover:bg-primary-500 hover:text-white rounded-lg transition-colors duration-200">
                            Request Product
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>