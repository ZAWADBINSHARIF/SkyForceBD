<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="bg-white border border-gray-200 rounded-2xl p-5">
    <h2 class="text-base font-medium text-gray-900 mb-5">How it works</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 lg:gap-0 relative">
        <div class="hidden lg:block absolute top-5 left-[10%] right-[10%] h-px bg-gray-200 z-0"></div>
        <template x-for="(step, i) in [
                      {icon:'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z', label:'Find product', sub:'Browse or search', bg:'#EEEDFE', ic:'#FE5265'},
                      {icon:'M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244', label:'Paste link', sub:'Submit product URL', bg:'#E1F5EE', ic:'#0F6E56'},
                      {icon:'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', label:'We verify', sub:'Team reviews it', bg:'#FAEEDA', ic:'#854F0B'},
                      {icon:'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z', label:'Pay securely', sub:'Choose your method', bg:'#E6F1FB', ic:'#185FA5'},
                      {icon:'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12', label:'Receive order', sub:'Delivered to your door', bg:'#EAF3DE', ic:'#3B6D11'}
                    ]" :key="i">
            <div class="flex flex-col items-center text-center relative z-10 px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center mb-3 shrink-0"
                    :style="'background:' + step.bg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                        :style="'color:' + step.ic">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="step.icon" />
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-800 mb-0.5" x-text="step.label"></p>
                <p class="text-[11px] text-gray-400 leading-snug" x-text="step.sub"></p>
            </div>
        </template>
    </div>
</div>