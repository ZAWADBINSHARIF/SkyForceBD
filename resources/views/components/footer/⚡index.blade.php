<?php

use App\Models\AdditionalPage;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

new class extends Component
{
    public ?Contact $contact = null;
    public Collection $footerPages;

    public function mount(): void
    {
        $this->contact = Contact::first();

        $this->footerPages = AdditionalPage::query()
            ->where('published', true)
            ->where('add_on_footer', true)
            ->orderBy('name')
            ->get(['name', 'slug']);
    }
};
?>

<footer class="bg-white border-t border-gray-100 pt-10 pb-5 px-4 md:px-8 font-sans">
    <div class="max-w-7xl mx-auto">

        {{-- Logo --}}
        <div class="mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <img src="{{ asset('images/skyforce-logo.png') }}" alt="ShopLink Logo" class="h-14 w-auto">
            </a>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-8">

            {{-- Column 1: Company Info --}}
            <div class="flex flex-col gap-4">

                @if ($contact?->head_office)
                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                        </svg>
                        Head Office:
                    </p>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $contact->head_office }}</p>
                </div>
                @endif

                @if ($contact?->shop_office)
                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                        </svg>
                        Shop:
                    </p>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $contact->shop_office }}</p>
                </div>
                @endif

                @if ($contact?->licence)
                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                        </svg>
                        License:
                    </p>
                    <p class="text-sm text-gray-500">{{ $contact->licence }}</p>
                </div>
                @endif

                @if ($contact?->email)
                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                        Email:
                    </p>
                    <a href="mailto:{{ $contact->email }}"
                        class="text-sm text-gray-500 hover:text-primary-500 transition-colors">
                        {{ $contact->email }}
                    </a>
                </div>
                @endif

                @if ($contact?->phone)
                <div>
                    <p class="flex items-center gap-1.5 text-xs font-bold text-gray-800 uppercase tracking-wide mb-1">
                        <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                        </svg>
                        Phone:
                    </p>
                    <a href="tel:{{ $contact->phone }}"
                        class="text-sm text-gray-500 hover:text-primary-500 transition-colors">
                        {{ $contact->phone }}
                    </a>
                </div>
                @endif
            </div>

            {{-- Column 2 and 3: Quick Links --}}
            <div class="col-span-2">
                <p class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">Quick Links</p>
                <ul class="grid grid-cols-2 gap-2.5">
                    @foreach ($footerPages as $page)
                    <li>
                        <a href="/page/{{ $page->slug }}"
                            class="text-sm text-gray-500 hover:text-primary-500 transition-colors">
                            {{ $page->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Column 4: Social + We Accept --}}
            <div class="flex flex-col gap-6 lg:col-start-4">

                {{-- Social Links --}}
                @if ($contact?->facebook || $contact?->instagram || $contact?->youtube)
                <div>
                    <p class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4">Social Links</p>
                    <div class="flex items-center gap-2">

                        @if ($contact?->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact->whatsapp) }}" target="_blank"
                            rel="noopener noreferrer" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white hover:bg-green-600
                            transition-colors">
                            <x-si-whatsapp class="w-4 h-4" />
                        </a>
                        @endif

                        @if ($contact?->facebook)
                        <a href="{{ $contact->facebook }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center
                                   text-blue-600 hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                            </svg>
                        </a>
                        @endif

                        @if ($contact?->instagram)
                        <a href="{{ $contact->instagram }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center
                                   text-pink-500 hover:bg-pink-50 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor"
                                    stroke-width="2" />
                                <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2" />
                                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
                            </svg>
                        </a>
                        @endif

                        @if ($contact?->youtube)
                        <a href="{{ $contact->youtube }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center
                                   text-red-500 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.97C5.12 20 12 20 12 20s6.88 0 8.59-.45a2.78 2.78 0 0 0 1.96-1.97A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" />
                                <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white" />
                            </svg>
                        </a>
                        @endif

                    </div>
                </div>
                @endif

                {{-- We Accept --}}
                <div>
                    <p class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">We Accept</p>
                    <img src="{{ asset('images/sslcommerz-we-accept.png') }}" alt="We accept payment info"
                        class="max-w-full h-auto">
                </div>

            </div>

        </div>

        {{-- Bottom Bar --}}
        <div
            class="border-t border-gray-100 pt-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-center">
            <p class="text-xs text-gray-400">© {{ date('Y') }} Sky Force BD. All rights reserved.</p>
        </div>

    </div>
</footer>