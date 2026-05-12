<?php

use App\Enums\AuthProviderName;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate('required|string|regex:/^[0-9+\-\s]{7,15}$/', message: 'Enter a valid phone number.')]
    public string $phone = '';

    #[Validate('required|string|min:6', message: 'Password must be at least 6 characters.')]
    public string $password = '';

    public bool $showPassword = false;
    public bool $loading = false;

    public function togglePassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function signIn(): void
    {
        $this->validate();

        $this->loading = true;

        $credentials = [
            'phone_number'    => preg_replace('/\D/', '', $this->phone),
            'password' => $this->password,
        ];

        if (Auth::guard('customer')->attempt($credentials)) {
            $this->dispatch('close-auth-modal');
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $this->loading = false;
        $this->addError('phone', 'Invalid phone number or password.');
    }

    public function redirectOauth(string $provider): void
    {
        abort_unless(in_array($provider, AuthProviderName::values()), 403);

        $this->redirect(route('oauth.redirect', $provider));
    }
};
?>

<div class="px-6 py-6 space-y-5 rounded-2xl">

    {{-- OAuth buttons --}}
    <div class="space-y-2.5">
        <button wire:click="redirectOauth('google')" type="button" class="w-full flex items-center justify-center gap-3 py-2.5 px-4 rounded-xl border border-gray-200
                        bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                <path fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
            </svg>
            Continue with Google
        </button>

        <button wire:click="redirectOauth('facebook')" type="button" class="w-full flex items-center justify-center gap-3 py-2.5 px-4 rounded-xl border border-gray-200
                        bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
            <svg class="w-4 h-4 shrink-0" fill="#1877F2" viewBox="0 0 24 24">
                <path
                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
            </svg>
            Continue with Facebook
        </button>
    </div>

    {{-- Divider --}}
    <div class="flex items-center gap-3">
        <div class="flex-1 h-px bg-gray-100"></div>
        <span class="text-xs text-gray-400 font-medium">or sign in with phone</span>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>

    {{-- Phone --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number</label>
        <input type="tel" wire:model.blur="phone" placeholder="+880 1XXXXXXXXX"
            class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-colors placeholder:text-gray-300
                        {{ $errors->has('phone') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
        @error('phone')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
        <div class="relative">
            <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model.blur="password" placeholder="••••••••"
                class="w-full px-3.5 py-2.5 pr-10 text-sm border rounded-xl outline-none transition-colors placeholder:text-gray-300
                            {{ $errors->has('password') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
            <button type="button" wire:click="togglePassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                @if ($showPassword)
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21" />
                </svg>
                @else
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                @endif
            </button>
        </div>
        @error('password')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Forgot --}}
    <div class="text-right -mt-3">
        <a href="#" class="text-xs text-primary-500 hover:text-primary-600 font-medium">Forgot password?</a>
    </div>

    {{-- Submit --}}
    <button wire:click="signIn" type="button" wire:loading.attr="disabled"
        class="btn-primary w-full py-3 rounded-xl font-semibold flex items-center justify-center gap-2">
        <span wire:loading.remove wire:target="signIn">Sign In</span>
        <span wire:loading wire:target="signIn" class="flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
        </span>
    </button>

    <p class="text-center text-xs text-gray-400 pb-1">
        Don't have an account?
        <button wire:click="$dispatch('open-auth-modal', { mode: 'signup' })"
            class="text-primary-500 font-semibold hover:text-primary-600">Create one</button>
    </p>
</div>