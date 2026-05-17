<?php

use App\Models\AdditionalPage;
use Livewire\Component;

new class extends Component
{
    public AdditionalPage $pageInfo;

    public function mount(string $slug)
    {
        $this->pageInfo = AdditionalPage::query()
            ->where('slug', $slug)
            ->where('published', true)
            ->first();
    }
};
?>

<div class="max-w-4xl mx-auto px-4 py-10">

    {{-- Title --}}
    <h1 class="text-3xl font-bold text-gray-900 mb-3">
        {{ $pageInfo?->name }}
    </h1>

    {{-- Meta --}}
    <div class="text-sm text-gray-500 mb-8">
        Last updated: {{ $pageInfo?->updated_at?->format('d M Y') }}
    </div>

    {{-- Content Card --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 md:p-10">

        @if($pageInfo?->content)
        <div class="prose max-w-none text-gray-700">
            {!! $pageInfo->content !!}
        </div>
        @else
        <p class="text-gray-500 text-sm">
            No content available for this page.
        </p>
        @endif

    </div>

</div>