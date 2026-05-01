<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public int $step = 1; // 1 = form, 2 = OTP

    // Step 1
    #[Validate('required|string|min:2', message: 'Name must be at least 2 characters.')]
    public string $name = '';

    #[Validate('required|string|regex:/^[0-9+\-\s]{7,15}$/|unique:users,phone', message: 'Enter a valid, unused phone number.')]
    public string $phone = '';

    #[Validate('required|string|min:6', message: 'Password must be at least 6 characters.')]
    public string $password = '';

    #[Validate('required|same:password', message: 'Passwords do not match.')]
    public string $passwordConfirmation = '';

    // Step 2 - OTP
    public string $otp = '';
    public string $otpSent = '';
    public int $otpExpiry = 0;
    public bool $otpResendable = false;

    public bool $showPassword = false;
    public bool $showConfirm = false;

    public function togglePassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }
    public function toggleConfirm(): void
    {
        $this->showConfirm  = ! $this->showConfirm;
    }

    public function sendOtp(): void
    {
        $this->validate([
            'name'                 => 'required|string|min:2',
            'phone'                => 'required|string|regex:/^[0-9+\-\s]{7,15}$/|unique:users,phone',
            'password'             => 'required|string|min:6',
            'passwordConfirmation' => 'required|same:password',
        ], [
            'name.required'                 => 'Name is required.',
            'name.min'                      => 'Name must be at least 2 characters.',
            'phone.required'                => 'Phone number is required.',
            'phone.regex'                   => 'Enter a valid phone number.',
            'phone.unique'                  => 'This phone number is already registered.',
            'password.required'             => 'Password is required.',
            'password.min'                  => 'Password must be at least 6 characters.',
            'passwordConfirmation.required' => 'Please confirm your password.',
            'passwordConfirmation.same'     => 'Passwords do not match.',
        ]);

        // Generate OTP
        $code = (string) random_int(100000, 999999);
        $this->otpSent   = $code;
        $this->otpExpiry = now()->addMinutes(5)->timestamp;
        $this->otpResendable = false;

        // TODO: send $code to $this->phone via SMS gateway

        $this->step = 2;

        // Allow resend after 60s (use a real timer in production)
        $this->dispatch('otp-sent');
    }

    public function verifyOtp(): void
    {
        if (empty($this->otp)) {
            $this->addError('otp', 'Please enter the OTP.');
            return;
        }

        if (now()->timestamp > $this->otpExpiry) {
            $this->addError('otp', 'OTP has expired. Please resend.');
            return;
        }

        if ($this->otp !== $this->otpSent) {
            $this->addError('otp', 'Invalid OTP. Please try again.');
            return;
        }

        $user = User::create([
            'name'     => $this->name,
            'phone'    => preg_replace('/\D/', '', $this->phone),
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        $this->dispatch('close-auth-modal');
        $this->redirect(route('home'), navigate: true);
    }

    public function resendOtp(): void
    {
        $code = (string) random_int(100000, 999999);
        $this->otpSent   = $code;
        $this->otpExpiry = now()->addMinutes(5)->timestamp;
        $this->resetErrorBag('otp');
        $this->otp = '';
        // TODO: send $code to $this->phone via SMS gateway
    }

    public function goBack(): void
    {
        $this->step = 1;
        $this->otp  = '';
        $this->resetErrorBag();
    }

    public function redirectOauth(string $provider): void
    {
        $this->redirect(route('oauth.redirect', $provider));
    }
};
?>

<div class="px-6 py-6">

    @if ($step === 1)

    {{-- OAuth --}}
    <div class="space-y-2.5 mb-5">
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
    <div class="flex items-center gap-3 mb-5">
        <div class="flex-1 h-px bg-gray-100"></div>
        <span class="text-xs text-gray-400 font-medium">or sign up with phone</span>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>

    <div class="space-y-4">
        {{-- Name --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name</label>
            <input type="text" wire:model.blur="name" placeholder="Your full name"
                class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-colors placeholder:text-gray-300
                                {{ $errors->has('name') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number</label>
            <input type="tel" wire:model.blur="phone" placeholder="+880 1XXXXXXXXX"
                class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-colors placeholder:text-gray-300
                                {{ $errors->has('phone') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
            <div class="relative">
                <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model.blur="password"
                    placeholder="Min. 6 characters"
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
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Confirm Password</label>
            <div class="relative">
                <input type="{{ $showConfirm ? 'text' : 'password' }}" wire:model.blur="passwordConfirmation"
                    placeholder="Re-enter your password"
                    class="w-full px-3.5 py-2.5 pr-10 text-sm border rounded-xl outline-none transition-colors placeholder:text-gray-300
                                    {{ $errors->has('passwordConfirmation') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
                <button type="button" wire:click="toggleConfirm"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    @if ($showConfirm)
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
            @error('passwordConfirmation') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <button wire:click="sendOtp" type="button" wire:loading.attr="disabled"
        class="btn-primary w-full py-3 rounded-xl font-semibold mt-5 flex items-center justify-center gap-2">
        <span wire:loading.remove wire:target="sendOtp">Send OTP & Continue</span>
        <span wire:loading wire:target="sendOtp" class="flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            Sending...
        </span>
    </button>

    <p class="text-center text-xs text-gray-400 mt-4 pb-1">
        Already have an account?
        <button wire:click="$dispatch('open-auth-modal', { mode: 'signin' })"
            class="text-primary-500 font-semibold hover:text-primary-600">Sign in</button>
    </p>

    @elseif ($step === 2)

    {{-- OTP Step --}}
    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 mb-1">Check your phone</h3>
        <p class="text-sm text-gray-500">
            We sent a 6-digit OTP to<br>
            <span class="font-semibold text-gray-700">{{ $phone }}</span>
        </p>
    </div>

    {{-- OTP input --}}
    <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5 text-center">Enter OTP</label>
        <input type="text" wire:model.blur="otp" placeholder="• • • • • •" maxlength="6" inputmode="numeric"
            class="w-full px-3.5 py-3 text-lg font-bold text-center border rounded-xl outline-none tracking-[0.4em] transition-colors
                            {{ $errors->has('otp') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
        @error('otp') <p class="text-xs text-red-500 mt-1 text-center">{{ $message }}</p> @enderror
    </div>

    <button wire:click="verifyOtp" type="button" wire:loading.attr="disabled"
        class="btn-primary w-full py-3 rounded-xl font-semibold flex items-center justify-center gap-2">
        <span wire:loading.remove wire:target="verifyOtp">Verify & Create Account</span>
        <span wire:loading wire:target="verifyOtp" class="flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            Verifying...
        </span>
    </button>

    <div class="flex items-center justify-between mt-4 pb-1">
        <button wire:click="goBack" type="button"
            class="text-xs text-gray-500 hover:text-gray-700 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </button>
        <button wire:click="resendOtp" type="button"
            class="text-xs text-primary-500 hover:text-primary-600 font-semibold">
            Resend OTP
        </button>
    </div>

    @endif
</div>