<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="pb-10">
    <div class="text-center mb-10">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Start Shopping From</h2>
        <p class="text-sm text-gray-500 mt-1">Get products delivered from your favorite global destinations</p>
    </div>

    <div class="flex justify-center items-center gap-6 md:gap-10 flex-wrap" x-data="{
                countries: [
                    { name: 'Thailand', code: 'th' },
                    { name: 'USA', code: 'us' },
                    { name: 'China', code: 'cn' },
                    { name: 'Dubai', code: 'ae' },
                    { name: 'Hong Kong', code: 'hk' },
                    { name: 'South Korea', code: 'kr' },
                    { name: 'United Kingdom', code: 'gb' }
                ]
            }">
        <template x-for="country in countries" :key="country.name">
            <div class="flex flex-col items-center group cursor-pointer">
                <div
                    class="relative w-24 h-16 md:w-32 md:h-20 overflow-hidden rounded-xl border border-gray-100 shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-primary-500 group-hover:-translate-y-1.5">
                    <img :src="`https://flagcdn.com/${country.code}.svg`" :alt="country.name"
                        class="w-full h-full object-cover">
                </div>

                <span
                    class="mt-4 text-xs md:text-sm font-bold text-gray-800 group-hover:text-primary-500 transition-colors"
                    x-text="country.name"></span>
            </div>
        </template>
    </div>
</div>