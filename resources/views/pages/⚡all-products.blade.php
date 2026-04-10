<?php

use Livewire\Component;

new class extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $sort = 'newest';
};
?>

<div class="bg-gray-50 min-h-screen" x-data="{
    search: '',
    category: '',
    sort: 'newest',
 
    categories: ['All', 'Electronics', 'Fashion', 'Sports', 'Home & Living', 'Beauty', 'Accessories'],
 
    products: [
        {name:'Wireless Earbuds', price:'৳4,999', oldPrice:'৳6,500', badge:'New', category:'Electronics', color:'#EEEDFE', img:'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=300&q=70'},
        {name:'Running Shoes', price:'৳8,900', oldPrice:'৳10,500', badge:'In stock', category:'Sports', color:'#E1F5EE', img:'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&q=70'},
        {name:'Smart Watch', price:'৳12,900', oldPrice:'৳15,000', badge:'New', category:'Electronics', color:'#FAEEDA', img:'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&q=70'},
        {name:'Game Controller', price:'৳6,550', oldPrice:'৳7,200', badge:'In stock', category:'Electronics', color:'#FAECE7', img:'https://images.unsplash.com/photo-1486401899868-0e435ed85128?w=300&q=70'},
        {name:'Mechanical Keyboard', price:'৳11,000', oldPrice:'৳13,500', badge:'New', category:'Electronics', color:'#E6F1FB', img:'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=300&q=70'},
        {name:'Desk Lamp', price:'৳3,499', oldPrice:'৳4,000', badge:'In stock', category:'Home & Living', color:'#EAF3DE', img:'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=300&q=70'},
        {name:'Sunglasses', price:'৳5,500', oldPrice:'৳7,000', badge:'New', category:'Accessories', color:'#FBEAF0', img:'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=300&q=70'},
        {name:'Backpack', price:'৳7,900', oldPrice:'৳9,500', badge:'In stock', category:'Accessories', color:'#F1EFE8', img:'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=300&q=70'},
        {name:'Yoga Mat', price:'৳2,999', oldPrice:'৳3,500', badge:'In stock', category:'Sports', color:'#E1F5EE', img:'https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=300&q=70'},
        {name:'Coffee Mug', price:'৳1,800', oldPrice:'৳2,200', badge:'New', category:'Home & Living', color:'#FAEEDA', img:'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=300&q=70'},
        {name:'Laptop Stand', price:'৳4,500', oldPrice:'৳6,000', badge:'In stock', category:'Electronics', color:'#EEEDFE', img:'https://images.unsplash.com/photo-1619472351888-f844a0b33f5b?w=300&q=70'},
        {name:'Phone Case', price:'৳1,599', oldPrice:'৳2,000', badge:'New', category:'Accessories', color:'#FAECE7', img:'https://images.unsplash.com/photo-1609654909515-0eeffe5e1513?w=300&q=70'},
        {name:'Sneakers', price:'৳9,500', oldPrice:'৳12,000', badge:'In stock', category:'Fashion', color:'#EAF3DE', img:'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=300&q=70'},
        {name:'Bluetooth Speaker', price:'৳6,600', oldPrice:'৳8,500', badge:'New', category:'Electronics', color:'#E6F1FB', img:'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&q=70'},
        {name:'Scented Candle', price:'৳2,200', oldPrice:'৳2,800', badge:'In stock', category:'Home & Living', color:'#FBEAF0', img:'https://images.unsplash.com/photo-1603903631918-a6e0a5ac5bfe?w=300&q=70'},
        {name:'Water Bottle', price:'৳2,600', oldPrice:'৳3,200', badge:'In stock', category:'Sports', color:'#E1F5EE', img:'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=300&q=70'},
        {name:'Notebook Set', price:'৳1,999', oldPrice:'৳2,500', badge:'New', category:'Accessories', color:'#FAEEDA', img:'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=300&q=70'},
        {name:'Wireless Charger', price:'৳4,200', oldPrice:'৳5,500', badge:'In stock', category:'Electronics', color:'#EEEDFE', img:'https://images.unsplash.com/photo-1618053448492-2b629c2c912f?w=300&q=70'},
        {name:'Lipstick Set', price:'৳3,100', oldPrice:'৳4,000', badge:'New', category:'Beauty', color:'#FBEAF0', img:'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=300&q=70'},
        {name:'Denim Jacket', price:'৳7,500', oldPrice:'৳9,000', badge:'In stock', category:'Fashion', color:'#E6F1FB', img:'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=300&q=70'},
        {name:'Gym Gloves', price:'৳2,499', oldPrice:'৳3,200', badge:'In stock', category:'Sports', color:'#EAF3DE', img:'https://images.unsplash.com/photo-1598971639058-a4578a28e4f9?w=300&q=70'},
        {name:'Desk Organizer', price:'৳3,200', oldPrice:'৳4,200', badge:'New', category:'Home & Living', color:'#F1EFE8', img:'https://images.unsplash.com/photo-1593642634315-48f5414c3ad9?w=300&q=70'},
        {name:'Perfume Bottle', price:'৳5,800', oldPrice:'৳7,500', badge:'New', category:'Beauty', color:'#FBEAF0', img:'https://images.unsplash.com/photo-1541643600914-78b084683702?w=300&q=70'},
        {name:'Leather Wallet', price:'৳3,500', oldPrice:'৳4,500', badge:'In stock', category:'Fashion', color:'#F1EFE8', img:'https://images.unsplash.com/photo-1627123424574-724758594e93?w=300&q=70'},
    ],
 
    get filtered() {
        return this.products.filter(p => {
            const matchSearch = p.name.toLowerCase().includes(this.search.toLowerCase());
            const matchCat = this.category === '' || this.category === 'All' || p.category === this.category;
            return matchSearch && matchCat;
        });
    }
}">

    {{-- Page Header --}}
    <div class="max-w-7xl mx-auto px-4 md:px-8 py-8">
        <div class="flex gap-8">

            {{-- Sidebar: Category Filter --}}
            <aside class="hidden md:flex flex-col gap-1 w-44 shrink-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Categories</p>
                <template x-for="cat in categories" :key="cat">
                    <button @click="category = cat === 'All' ? '' : cat" :class="(category === '' && cat === 'All') || category === cat
                            ? 'bg-primary-50 text-primary-600 font-semibold border-primary-200'
                            : 'text-gray-500 hover:bg-gray-100 border-transparent'"
                        class="text-left text-sm px-3 py-2 rounded-xl border transition-colors duration-150"
                        x-text="cat"></button>
                </template>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">

                {{-- Mobile Category Filter --}}
                <div class="flex gap-2 overflow-x-auto pb-2 mb-5 md:hidden scrollbar-hide">
                    <template x-for="cat in categories" :key="cat">
                        <button @click="category = cat === 'All' ? '' : cat" :class="(category === '' && cat === 'All') || category === cat
                                ? 'bg-primary-500 text-white'
                                : 'bg-white text-gray-500 border border-gray-200'"
                            class="text-xs font-semibold px-4 py-1.5 rounded-full whitespace-nowrap transition-colors duration-150 shrink-0"
                            x-text="cat"></button>
                    </template>
                </div>

                {{-- Product Grid --}}
                <div x-show="filtered.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    <template x-for="(p, i) in filtered" :key="i">
                        <div x-data="{ isWished: false }"
                            class="bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-md hover:border-gray-300 transition-all duration-300 group flex flex-col">
                            {{-- Image --}}
                            <div class="relative aspect-square w-full overflow-hidden shrink-0"
                                :style="'background:' + p.color">
                                <img :src="p.img" :alt="p.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                {{-- Wishlist --}}
                                <button @click.prevent="isWished = !isWished"
                                    class="absolute top-2 right-2 p-1.5 rounded-full bg-white/80 backdrop-blur-sm shadow-sm hover:bg-white transition-all z-10 active:scale-90">
                                    <svg x-show="!isWished" class="w-4 h-4 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

                                {{-- Badge --}}
                                <span class="absolute top-2 left-2 text-[9px] font-semibold px-2 py-0.5 rounded-full"
                                    :class="p.badge === 'New' ? 'bg-primary-50 text-primary-600' : 'bg-green-50 text-green-700'"
                                    x-text="p.badge"></span>
                            </div>

                            {{-- Info --}}
                            <div class="p-2.5 flex flex-col flex-1">
                                <p class="text-xs font-medium text-gray-800 truncate mb-1" x-text="p.name"></p>
                                <p class="text-[10px] text-gray-400 mb-2" x-text="p.category"></p>

                                <div class="flex items-end justify-between mb-3">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-400 line-through leading-none mb-0.5"
                                            x-text="p.oldPrice"></span>
                                        <span class="text-sm font-bold text-primary-500 leading-none"
                                            x-text="p.price"></span>
                                    </div>
                                </div>

                                <div class="flex gap-1.5 mt-auto">
                                    <button
                                        class="flex-1 py-1.5 text-xs font-semibold text-white bg-primary-500 hover:bg-primary-600 rounded-lg transition-colors duration-200">
                                        Request
                                    </button>
                                    <a href="/product"
                                        class="flex-1 py-1.5 text-xs font-semibold text-center text-primary-500 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors duration-200">
                                        Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty State --}}
                <div x-show="filtered.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">No products found</p>
                    <p class="text-xs text-gray-400 mt-1">Try a different search or category</p>
                    <button @click="search = ''; category = ''"
                        class="mt-4 text-xs font-semibold text-primary-500 hover:underline">
                        Clear filters
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>