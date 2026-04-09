<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<footer class="bg-white border-t border-gray-200 px-4 md:px-6 pt-8 pb-5">
    <div class="container mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-6">
        <div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">Follow us</p>
            <div class="flex gap-2 flex-wrap mb-3">
                <template x-for="s in ['f','in','𝕏','▶','w']" :key="s">
                    <div class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-xs text-gray-500 cursor-pointer hover:bg-gray-50 transition-colors"
                        x-text="s"></div>
                </template>
            </div>
            <p class="text-xs text-gray-400">@shoplink.official</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">Contact us</p>
            <div class="flex flex-col gap-1.5">
                <template x-for="c in ['support@shoplink.com','+1 (800) 555-0123','Live chat — 9am–6pm','Help center']"
                    :key="c">
                    <a class="text-sm text-gray-500 hover:text-gray-800 cursor-pointer transition-colors"
                        x-text="c"></a>
                </template>
            </div>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">Our policies</p>
            <div class="flex flex-col gap-1.5">
                <template
                    x-for="p in ['Privacy policy','Terms of service','Return & refund','Shipping policy','Cookie policy']"
                    :key="p">
                    <a class="text-sm text-gray-500 hover:text-gray-800 cursor-pointer transition-colors"
                        x-text="p"></a>
                </template>
            </div>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">We accept</p>
            <div class="flex flex-wrap gap-1.5">
                <template x-for="m in ['Visa','Mastercard','PayPal','bKash','Nagad','COD']" :key="m">
                    <span class="text-xs px-2.5 py-1 border border-gray-200 rounded-md text-gray-500" x-text="m"></span>
                </template>
            </div>
        </div>
    </div>
    <div
        class="max-w-7xl mx-auto border-t border-gray-100 pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-center">
        <p class="text-xs text-gray-400">© 2026 ShopLink. All rights reserved.</p>
        <p class="text-xs text-gray-400">Made with care for our customers</p>
    </div>
</footer>