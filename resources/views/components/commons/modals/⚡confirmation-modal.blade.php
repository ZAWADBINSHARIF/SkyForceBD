<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?string $title = null;
    public ?string $message = null;

    public string $confirmText = 'Confirm';
    public string $cancelText = 'Cancel';

    public string $confirmColor = 'red';

    public bool $show = false;

    public ?string $callEvent = null;

    public array $eventPayload = [];

    #[On('open-confirmation-modal')]
    public function open(
        string $title,
        string $message,
        ?string $callEvent = null,
        array $eventPayload = [],
        string $confirmText = 'Confirm',
        string $cancelText = 'Cancel',
        string $confirmColor = 'red',
    ): void {
        $this->title = $title;
        $this->message = $message;

        $this->callEvent = $callEvent;
        $this->eventPayload = $eventPayload;

        $this->confirmText = $confirmText;
        $this->cancelText = $cancelText;

        $this->confirmColor = $confirmColor;

        $this->show = true;
    }

    public function confirm(): void
    {
        if ($this->callEvent) {
            $this->dispatch($this->callEvent, ...$this->eventPayload);
        }

        $this->close();
    }

    public function close(): void
    {
        $this->reset([
            'show',
            'title',
            'message',
            'callEvent',
            'eventPayload',
        ]);

        $this->confirmText = 'Confirm';
        $this->cancelText = 'Cancel';
        $this->confirmColor = 'red';
    }

    public function getConfirmButtonClassesProperty(): string
    {
        return match ($this->confirmColor) {
            'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
            'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'yellow' => 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400',
            default => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        };
    }
};
?>

<div>
    @if ($show)

    {{-- Backdrop --}}
    <div wire:click="close" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"></div>

        {{-- Modal --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <div x-data x-on:keydown.escape.window="$wire.close()"
                class="relative w-full sm:max-w-md bg-white rounded-t-3xl rounded-3xl shadow-2xl overflow-hidden animate-in slide-in-from-bottom duration-200">

                {{-- Header --}}
                <div class="flex items-start gap-4 p-6 border-b border-gray-100">

                    {{-- Icon --}}
                    <div class="shrink-0 w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v4m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.67 18h16.66a1 1 0 00.88-1.5l-7.5-13a1 1 0 00-1.74 0z" />
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 pr-8">
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $title }}
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-gray-600">
                            {{ $message }}
                        </p>
                    </div>

                    {{-- Close --}}
                    <button wire:click="close"
                        class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                </div>

                {{-- Footer --}}
                <div class="flex flex-col-reverse sm:flex-row gap-3 p-6 bg-gray-50">

                    {{-- Cancel --}}
                    <button wire:click="close" type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                        {{ $cancelText }}
                    </button>

                    {{-- Confirm --}}
                    <button wire:click="confirm" type="button"
                        class="w-full sm:flex-1 inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold text-white transition focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $this->confirmButtonClasses }}">
                        {{ $confirmText }}
                    </button>

                </div>

            </div>

        </div>

    @endif
</div>