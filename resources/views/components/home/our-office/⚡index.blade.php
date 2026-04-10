<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

{{-- Office Section --}}
<section class="bg-gray-50 py-5 px-4 md:px-8">
        {{-- Section Header --}}
        <div class="flex flex-col items-center text-center mb-10">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-0.5 bg-primary-500 rounded-full"></span>
                <span class="text-xs font-semibold text-primary-500 uppercase tracking-widest">Our Offices</span>
                <span class="w-6 h-0.5 bg-primary-500 rounded-full"></span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                We Operate <span class="text-primary-500">Globally</span>
            </h2>
            <p class="text-sm text-gray-400 mt-2 max-w-md">Two offices, one mission — delivering the best to your
                doorstep.</p>
        </div>

        {{-- Office Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            @foreach([
            [
            'country' => 'Bangladesh',
            'flag' => '🇧🇩',
            'label' => 'Head Office',
            'name' => 'ShopLink Bangladesh HQ',
            'image' => 'https://bdbel.com/wp-content/uploads/2025/01/modern-office-environment-in-Bangladesh-with-a-skyline-of-Dhaka-in-the-background.-The-image-shows-a-well-lit-office-interior-with.webp',
            'address' => 'New Elephant Road, Popular Tower, 7th Floor, Dhaka 1205',
            'phone' => '+880 1700-000000',
            'email' => 'bd@shoplink.com',
            'hours' => 'Sat – Thu: 9:00 AM – 6:00 PM',
            'closed' => 'Friday: Closed',
            'maps' => 'https://maps.google.com/?q=New+Elephant+Road+Dhaka',
            ],
            [
            'country' => 'China',
            'flag' => '🇨🇳',
            'label' => 'Sourcing Office',
            'name' => 'ShopLink China Office',
            'image' => 'https://skyforcebd.com/wp-content/uploads/2025/12/ChatGPT-Image-Apr-20-2025-12_10_43-AM-3-1.png',
            'address' => 'Yiwu International Trade City, Zhejiang Province, China',
            'phone' => '+86 579-0000-0000',
            'email' => 'cn@shoplink.com',
            'hours' => 'Mon – Fri: 9:00 AM – 6:00 PM',
            'closed' => 'Sat & Sun: Closed',
            'maps' => 'https://maps.google.com/?q=Yiwu+International+Trade+City',
            ],
            ] as $office)
            <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">

                {{-- Office Image --}}
                <div class="aspect-video w-full overflow-hidden relative">
                    <img src="{{ asset($office['image']) }}" alt="{{ $office['country'] }} Office"
                        class="w-full h-full object-cover">
                    {{-- Country badge over image --}}
                    <div
                        class="absolute top-4 left-4 flex items-center gap-1.5 bg-white/90 backdrop-blur-sm border border-gray-100 rounded-xl px-3 py-1.5">
                        <span class="text-base leading-none">{{ $office['flag'] }}</span>
                        <span class="text-xs font-bold text-gray-800">{{ $office['country'] }}</span>
                        <span class="text-[10px] text-gray-400 border-l border-gray-200 pl-1.5">{{ $office['label']
                            }}</span>
                    </div>
                </div>

                {{-- Info --}}
                <div class="px-6 py-6 flex flex-col gap-4">

                    <h3 class="text-base font-bold text-gray-900">{{ $office['name'] }}</h3>

                    <div class="flex flex-col gap-3.5">

                        {{-- Address --}}
                        <div class="flex items-start gap-3">
                            <span
                                class="w-7 h-7 rounded-lg bg-primary-50 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                                </svg>
                            </span>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $office['address'] }}</p>
                        </div>

                        {{-- Phone --}}
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-primary-50 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.58a1 1 0 0 1-.25 1.01l-2.2 2.2z" />
                                </svg>
                            </span>
                            <a href="tel:{{ preg_replace('/\s/', '', $office['phone']) }}"
                                class="text-sm text-gray-600 hover:text-primary-500 transition-colors">{{
                                $office['phone'] }}</a>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-primary-50 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-primary-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                            </span>
                            <a href="mailto:{{ $office['email'] }}"
                                class="text-sm text-gray-600 hover:text-primary-500 transition-colors">{{
                                $office['email'] }}</a>
                        </div>

                        {{-- Hours --}}
                        <div class="flex items-start gap-3">
                            <span class="w-7 h-7 rounded-lg bg-primary-50 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm text-gray-600">{{ $office['hours'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $office['closed'] }}</p>
                            </div>
                        </div>

                    </div>

                    {{-- Get Directions --}}
                    <a href="{{ $office['maps'] }}" target="_blank"
                        class="inline-flex items-center gap-2 border border-primary-400 text-primary-500 hover:bg-primary-500 hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors duration-150 self-start mt-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                        Get Directions
                    </a>

                </div>
            </div>
            @endforeach

        </div>

</section>
