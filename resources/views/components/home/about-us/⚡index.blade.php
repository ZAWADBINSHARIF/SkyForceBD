<?php

use App\Models\AboutUs;
use Livewire\Component;

new class extends Component
{
    public AboutUs $aboutUs;

    public function mount()
    {
        /**
         * @var AboutUs $aboutUs
         */
        $aboutUs = AboutUs::first();

        if ($aboutUs) {
            $this->aboutUs = $aboutUs;
        }
    }
};
?>

{{-- About Section --}}
<section class="bg-white py-5 px-4 md:px-8 border border-gray-200 rounded-2xl">
    @if ($aboutUs)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        {{-- Left: Image --}}
        @if ($aboutUs->image_url)
        <div class="relative">
            <div class="rounded-2xl overflow-hidden aspect-4/5 w-full max-w-md mx-auto lg:mx-0">
                <img src="{{ Storage::url($aboutUs->image_url) }}" alt="About Us"
                    class="w-full h-full object-cover rotate-y-180">
            </div>
        </div>
        @endif

        {{-- Right: Content --}}
        <div class="flex flex-col gap-5">

            {{-- Label --}}
            <div class="flex items-center gap-2">
                <span class="w-6 h-0.5 bg-primary-500 rounded-full"></span>
                <span class="text-xs font-semibold text-primary-500 uppercase tracking-widest">About Us</span>
            </div>

            {{-- Heading --}}
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                {{$aboutUs->heading}} <br>
                <span class="text-primary-500">{{$aboutUs->heading_highlight}}</span>
            </h2>

            {{-- Body --}}
            <p class="text-gray-500 text-sm leading-relaxed">
                {{$aboutUs->body}}
            </p>

            {{-- Feature list --}}
            <ul class="flex flex-col gap-3 mt-1">
                @foreach($aboutUs->features as $item)
                <li class="flex items-center gap-3">
                    <span class="w-5 h-5 rounded-full bg-primary-50 flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-primary-500" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span class="text-sm text-gray-600">{{ $item['feature'] }}</span>
                </li>
                @endforeach
            </ul>

            {{-- CTA --}}
            @if ($aboutUs->cta_label && $aboutUs->cta_link)
            <div class="mt-2">
                <a href="{{$aboutUs->cta_link}}"
                    class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold px-6 py-3 rounded-xl transition-colors duration-150">
                    {{$aboutUs->cta_label}}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
            @endif

        </div>

    </div>
    @endif

</section>