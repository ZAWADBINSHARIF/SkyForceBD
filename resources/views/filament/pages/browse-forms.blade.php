<x-filament-panels::page>

    @if (! $this->activeSlug)
        {{-- ===================== BROWSE MODE ===================== --}}
        @php $forms = $this->getAvailableForms(); @endphp

        @if ($forms->isEmpty())
            <div class="text-center py-16">
                <x-heroicon-o-clipboard-document-list class="mx-auto h-10 w-10 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No forms available</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check back later.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($forms as $form)
                    <button
                        type="button"
                        wire:click="selectForm('{{ $form->slug }}')"
                        class="text-left rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-5 shadow-sm hover:shadow-md hover:border-primary-400 transition-all"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-gray-950 dark:text-white">
                                {{ $form->name }}
                            </h3>
                            <x-heroicon-o-arrow-right class="h-4 w-4 text-gray-400 shrink-0 mt-1" />
                        </div>

                        @if ($form->description)
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-3">
                                {{ $form->description }}
                            </p>
                        @endif

                        <div class="mt-4 flex items-center gap-1 text-xs font-medium text-gray-400">
                            <x-heroicon-o-list-bullet class="h-3.5 w-3.5" />
                            {{ count($form->fields ?? []) }} fields
                        </div>
                    </button>
                @endforeach
            </div>
        @endif

    @else
        {{-- ===================== FILL MODE ===================== --}}
        @php $activeForm = $this->getActiveForm(); @endphp

        <div class="max-w-2xl">
            <button
                type="button"
                wire:click="backToList"
                class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                <x-heroicon-o-arrow-left class="h-4 w-4" />
                Back to all forms
            </button>

            @if (! $activeForm)
                <div class="rounded-xl border border-gray-200 dark:border-white/10 p-6 text-sm text-gray-500">
                    This form could not be found.
                </div>
            @elseif ($this->submitted)
                <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-8 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/10">
                        <x-heroicon-o-check class="h-6 w-6 text-green-600 dark:text-green-400" />
                    </div>
                    <h2 class="mt-4 text-lg font-semibold text-gray-950 dark:text-white">
                        {{ $activeForm->settings['success_message'] ?? 'Thanks! Your response has been recorded.' }}
                    </h2>
                    <button
                        type="button"
                        wire:click="backToList"
                        class="mt-4 text-sm font-medium text-primary-600 hover:text-primary-500"
                    >
                        Fill out another form
                    </button>
                </div>
            @elseif (! $activeForm->isOpen())
                <div class="rounded-xl border border-gray-200 dark:border-white/10 p-6">
                    <h2 class="font-semibold text-gray-950 dark:text-white">Not currently accepting responses</h2>
                    <p class="mt-1 text-sm text-gray-500">Please check back later.</p>
                </div>
            @else
                <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-6 sm:p-8">
                    <h1 class="text-xl font-bold text-gray-950 dark:text-white">{{ $activeForm->name }}</h1>

                    @if ($activeForm->description)
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $activeForm->description }}</p>
                    @endif

                    <form wire:submit="submit" class="mt-6">
                        {{ $this->form }}

                        <div class="mt-6">
                            <x-filament::button type="submit">
                                {{ $activeForm->settings['submit_button_text'] ?? 'Submit' }}
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    @endif

</x-filament-panels::page>
