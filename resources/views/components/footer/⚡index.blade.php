<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<footer class="bg-white border-t border-gray-100 pt-10 pb-5 px-4 md:px-8 font-sans">
    <div class="max-w-7xl mx-auto">

        {{-- Logo --}}
        <div class="mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                {{-- Replace with your actual logo image --}}
                <img src="{{ asset('images/skyforce-logo.png') }}" alt="ShopLink Logo" class="h-14 w-auto">
            </a>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-8">

            {{-- Column 1: Company Info --}}
            <div class="flex flex-col gap-4">

                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                        </svg>
                        Head Office:
                    </p>
                    <p class="text-sm text-gray-500 leading-relaxed">New Elephant road, Popular Tower, 7th floor.</p>
                </div>

                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                        </svg>
                        Shop:
                    </p>
                    <p class="text-sm text-gray-500 leading-relaxed">Elephant road, Coffe House Road, ShopLink office
                        Tower, 6th floor.</p>
                </div>

                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                        </svg>
                        License:
                    </p>
                    <p class="text-sm text-gray-500">TRAD / DSCC / 00654 / 2021</p>
                </div>

                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                        Email:
                    </p>
                    <a href="mailto:info@shoplink.com"
                        class="text-sm text-gray-500 hover:text-primary-500 transition-colors">info@shoplink.com</a>
                </div>

            </div>

            {{-- Column 2: Quick Links --}}
            <div>
                <p class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">Quick Links</p>
                <ul class="flex flex-col gap-2.5">
                    @foreach(['About Us', 'Contact Us', 'Blog', 'My Account'] as $link)
                    <li>
                        <a href="#" class="text-sm text-gray-500 hover:text-primary-500 transition-colors">{{ $link
                            }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Column 3: Information --}}
            <div>
                <p class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">Information</p>
                <ul class="flex flex-col gap-2.5">
                    @foreach(['Disclaimer', 'Refund and Returns Policy', 'Delivery', 'Privacy Policy', 'Terms &
                    Conditions'] as $link)
                    <li>
                        <a href="#" class="text-sm text-gray-500 hover:text-primary-500 transition-colors">{{ $link
                            }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Column 4: Social + We Accept --}}
            <div class="flex flex-col gap-6">

                {{-- Social Links --}}
                <div>
                    <p class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">Social Links</p>
                    <div class="flex items-center gap-2">
                        {{-- Facebook --}}
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                            </svg>
                        </a>
                        {{-- Instagram --}}
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-pink-500 hover:bg-pink-50 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor"
                                    stroke-width="2" />
                                <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2" />
                                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
                            </svg>
                        </a>
                        {{-- YouTube --}}
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.97C5.12 20 12 20 12 20s6.88 0 8.59-.45a2.78 2.78 0 0 0 1.96-1.97A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" />
                                <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- We Accept --}}
                <div>
                    <p class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">We Accept</p>
                    <div class="flex flex-wrap gap-1.5">
                       <img
                       src="{{asset('images/sslcommerz-we-accept.png')}}"
                       alt="We accept payment info"
                       
                       />
                    </div>
                </div>

            </div>

        </div>

        {{-- Bottom Bar --}}
        <div
            class="border-t border-gray-100 pt-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-center">
            <p class="text-xs text-gray-400">© {{ date('Y') }} ShopLink. All rights reserved.</p>
            <p class="text-xs text-gray-400">Made with care for our customers</p>
        </div>

    </div>
</footer>