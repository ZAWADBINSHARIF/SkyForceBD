<x-filament-panels::page>
    <x-filament::tabs>
        <x-filament::tabs.item :active="$this->activeTab === 'contact'" wire:click="$set('activeTab', 'contact')"
            icon="heroicon-o-phone">
            Contact
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$this->activeTab === 'about'" wire:click="$set('activeTab', 'about')"
            icon="heroicon-o-information-circle">
            About Us
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$this->activeTab === 'countries'" wire:click="$set('activeTab', 'countries')"
            icon="heroicon-o-globe-alt">
            Countries
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$this->activeTab === 'offices'" wire:click="$set('activeTab', 'offices')"
            icon="heroicon-o-building-office-2">
            Offices
        </x-filament::tabs.item>

        <x-filament::tabs.item :active="$this->activeTab === 'ads_videos'" wire:click="$set('activeTab', 'ads_videos')"
            icon="heroicon-o-play-circle">
            Videos
        </x-filament::tabs.item>

    </x-filament::tabs>

    <div class="mt-6">
        @if ($this->activeTab === 'contact')
        {{ $this->contactForm }}
        @elseif ($this->activeTab === 'about')
        {{ $this->aboutForm }}
        @elseif ($this->activeTab === 'countries')
        {{ $this->countryForm }}
        @elseif ($this->activeTab === 'offices')
        {{ $this->officeForm }}
        @elseif ($this->activeTab === 'ads_videos')
        {{ $this->adsVideoForm }}
        @endif
    </div>
</x-filament-panels::page>