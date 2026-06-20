<?php

use App\Models\Country;
use Livewire\Component;

new class extends Component
{

    public array $countries;

    public function mount()
    {
        $countries = Country::first();

        if ($countries) {
            $this->countries = $countries->country;
        }
    }
};
?>

<div class="pb-0">
    <div class="text-center mb-10">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Start Shopping From</h2>
        <p class="text-sm text-gray-500 mt-1">Get products delivered from your favorite global destinations</p>
    </div>

    <div class="flex justify-center items-center gap-4 md:gap-6 flex-wrap">
        @foreach ($countries as $country)

        @if ($country['code'])
        <a href="{{$country['url']}}">
            <div class="flex flex-col items-center group cursor-pointer">
                <div
                    class="relative w-15 h-10 md:w-25 md:h-15 overflow-hidden rounded-lg border border-gray-100 shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-primary-500 group-hover:-translate-y-1.5">
                    <img src="https://flagcdn.com/{{$country['code']}}.svg" alt="{{$country['code']}}"
                        class="w-full h-full object-cover">
                </div>

                <span
                    class="mt-1 text-xs md:text-sm font-bold text-gray-800 group-hover:text-primary-500 transition-colors"
                    >{{$country["name"]}}</span>
            </div>
        </a>
        @endif

        @endforeach

    </div>
</div>