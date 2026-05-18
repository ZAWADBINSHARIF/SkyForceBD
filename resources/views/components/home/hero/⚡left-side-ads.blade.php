<?php

use App\Models\Banner;
use Livewire\Component;

new class extends Component
{
    public Banner $banner;

    public function mount()
    {
        $this->banner = Banner::orderBy('sort_order', 'desc')
            ->first();
    }
};
?>

<div class="hidden md:block col-span-2 rounded-2xl overflow-hidden aspect-5/5.4 bg-gray-200 ">
    @if($banner)
    <img src="{{ asset('storage/' . $banner->image) }}" class="w-full h-full" alt="Featured">
    @endif
</div>