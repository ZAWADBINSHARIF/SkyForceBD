<?php

use App\Enums\Modal;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public bool $show = false;
    public string $mode = Modal::SIGNIN->value; // signin | signup
    public bool $closeDisable = false;

    #[On('open-auth-modal')]
    public function open(string $mode = Modal::SIGNIN->value, bool $closeDisable = false): void
    {
        $this->mode = Modal::from($mode)->value;
        $this->show = true;
        $this->closeDisable = $closeDisable;
    }

    public function close()
    {
        if ($this->closeDisable)
            return null;

        $this->show = false;
    }

    public function switchMode(string $mode): void
    {
        $this->mode = Modal::from($mode)->value;
        $this->dispatch('auth-mode-changed', mode: $mode);
    }
};
?>

<div>
    @if ($show)
    {{-- Backdrop --}}
    <div wire:click="close" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"></div>

    {{-- Modal shell --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
        <div
            class="relative w-full sm:max-w-md bg-white rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden max-h-[70dvh] overflow-y-auto">

            {{-- Close button --}}
            <button wire:click="close"
                wire:show="!closeDisable" class="absolute top-4 right-4 z-10 w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Tab switcher --}}
            <div class="flex border-b border-gray-100">
                <button wire:click="switchMode('signin')"
                    class="flex-1 py-4 text-sm font-semibold transition-colors relative
                                    {{ $mode === 'signin' ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600' }}">
                    Sign In
                    @if ($mode === 'signin')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-500"></span>
                    @endif
                </button>
                <button wire:click="switchMode('signup')"
                    class="flex-1 py-4 text-sm font-semibold transition-colors relative
                                    {{ $mode === 'signup' ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600' }}">
                    Create Account
                    @if ($mode === 'signup')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-500"></span>
                    @endif
                </button>
            </div>

            {{-- Child component --}}
            @if ($mode === 'signin')
            <livewire:auth.sign-in :key="'signin'" />
            @else
            <livewire:auth.sign-up :key="'signup'" />
            @endif

        </div>
    </div>
    @endif
</div>