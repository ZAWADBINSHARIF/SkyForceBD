<?php

use App\Models\Customer;
use App\Services\BulkSMSBDService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    // Modal state
    public bool   $show      = false;
    public string $activeTab = 'profile';

    // Profile fields
    public string $full_name    = '';
    public string $address      = '';
    public string $phone_number = '';

    // Phone OTP flow
    public string $pending_phone = '';       // new number waiting for OTP
    public bool   $showOtpField  = false;
    public string $phone_otp     = '';

    // Password fields
    public string $current_password          = '';
    public string $new_password              = '';
    public string $new_password_confirmation = '';

    // Feedback
    public string $profileSuccess = '';
    public string $passwordSuccess = '';
    public string $phoneOtpError  = '';

    // ── Boot ────────────────────────────────────────────────────────

    public function mount(): void
    {
        $user = Auth::guard('customer')->user();

        $this->full_name    = $user->full_name    ?? '';
        $this->address      = $user->address      ?? '';
        $this->phone_number = $user->phone_number ?? '';
    }

    // ── Modal ────────────────────────────────────────────────────────

    #[On('open-profile-modal')]
    public function openModal(): void
    {
        $this->show = true;
    }

    #[On('close-profile-modal')]
    public function closeModal(): void
    {
        $this->show = false;
        $this->resetOtpFlow();
        $this->resetFeedback();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetOtpFlow();
        $this->resetFeedback();
        $this->resetErrorBag();
    }

    // ── Profile save ─────────────────────────────────────────────────

    public function saveProfile(): void
    {
        $this->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'address'   => ['nullable', 'string', 'max:255'],
        ], [
            'full_name.required' => 'Full name is required.',
        ]);

        /** @var Customer $user */
        $user = Auth::guard('customer')->user();

        $newPhone = trim($this->phone_number);
        // Phone changed — start OTP flow instead of saving directly
        if ($newPhone !== $user->phone_number) {
            $this->validateOnly('phone_number', [
                'phone_number' => [
                    'required',
                    'regex:/^[0-9\+\-\(\)\s]{7,20}$/',
                    'unique:customers,phone_number',
                    'max:11'
                ],
            ], [
                'phone_number.regex'  => 'Enter a valid phone number.',
                'phone_number.unique' => 'This number is already registered.',
            ]);

            // Save name/address immediately, hold phone until OTP confirmed
            $user->update([
                'full_name' => $this->full_name,
                'address'   => $this->address,
            ]);

            // dd($newPhone);
            $this->sendPhoneOtp($newPhone);
            return;
        }

        $user->update([
            'full_name' => $this->full_name,
            'address'   => $this->address,
        ]);

        $this->profileSuccess = 'Profile updated successfully.';
    }

    // ── Phone OTP ─────────────────────────────────────────────────────

    private function otpCacheKey(string $phone): string
    {
        return 'otp:phone_update:' . preg_replace('/\D/', '', $phone);
    }

    private function sendPhoneOtp(string $phone): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put($this->otpCacheKey($phone), $code, now()->addMinutes(5));

        $sms      = app(BulkSMSBDService::class);
        $response = $sms->send(
            numbers: [$phone],
            message: "Your Sky Force BD verification code is: {$code}. Valid for 5 minutes.",
        );

        if (! $response->successful()) {
            $this->addError('phone_number', 'Failed to send OTP: ' . $response->errorLabel());
            return;
        }

        $this->pending_phone = $phone;
        $this->showOtpField  = true;
        $this->phone_otp     = '';
        $this->phoneOtpError = '';
    }

    public function verifyPhoneOtp(): void
    {
        if (empty($this->phone_otp)) {
            $this->phoneOtpError = 'Please enter the OTP.';
            return;
        }

        $cached = Cache::get($this->otpCacheKey($this->pending_phone));

        if ($cached === null) {
            $this->phoneOtpError = 'OTP has expired. Please resend.';
            return;
        }

        if ($this->phone_otp !== $cached) {
            $this->phoneOtpError = 'Invalid OTP. Please try again.';
            return;
        }

        Cache::forget($this->otpCacheKey($this->pending_phone));

        /** @var Customer $user */
        $user = Auth::guard('customer')->user();

        $user->update(['phone_number' => $this->pending_phone]);

        $this->phone_number = $this->pending_phone;
        $this->resetOtpFlow();
        $this->profileSuccess = 'Profile and phone number updated successfully.';
    }

    public function resendPhoneOtp(): void
    {
        if (Cache::has($this->otpCacheKey($this->pending_phone))) {
            $this->phoneOtpError = 'Please wait before requesting a new OTP.';
            return;
        }

        $this->sendPhoneOtp($this->pending_phone);
    }

    public function cancelOtp(): void
    {
        /** @var Customer $user */
        $user = Auth::guard('customer')->user();

        // Restore the original phone in the field
        $this->phone_number = $user->phone_number ?? '';
        $this->resetOtpFlow();
    }

    // ── Password ──────────────────────────────────────────────────────

    public function changePassword(): void
    {
        $this->validate([
            'current_password'          => ['required'],
            'new_password'              => ['required', 'min:6', 'confirmed'],
            'new_password_confirmation' => ['required'],
        ], [
            'new_password.confirmed' => 'Passwords do not match.',
        ]);

        /** @var Customer $user */
        $user = Auth::guard('customer')->user();

        if (! Hash::check($this->current_password, $user->password_hash)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->update(['password_hash' => Hash::make($this->new_password)]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->passwordSuccess = 'Password changed successfully.';
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function resetOtpFlow(): void
    {
        $this->showOtpField  = false;
        $this->pending_phone = '';
        $this->phone_otp     = '';
        $this->phoneOtpError = '';
    }

    private function resetFeedback(): void
    {
        $this->profileSuccess = '';
        $this->passwordSuccess = '';
        $this->phoneOtpError  = '';
    }
};
?>

<div>
    @if ($show)
    <div x-data x-on:keydown.escape.window="$dispatch('close-profile-modal')"
        class="fixed inset-0 z-40 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$dispatch('close-profile-modal')"></div>

        {{-- Panel --}}
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl
                    ring-1 ring-slate-200 overflow-hidden" role="dialog" aria-modal="true" aria-label="Edit Profile">

            {{-- ── Header ── --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-bold select-none">
                        {{ strtoupper(substr(Auth::guard('customer')->user()->full_name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 leading-tight">Account Settings</p>
                        <p class="text-xs text-slate-400">{{ Auth::guard('customer')->user()->phone_number ?? '' }}</p>
                    </div>
                </div>
                <button wire:click="$dispatch('close-profile-modal')"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- ── Tab Bar ── --}}
            <div class="flex border-b border-slate-100 px-6 gap-1">
                @foreach ([['profile', 'Profile'], ['password', 'Password']] as [$key, $label])
                <button wire:click="setTab('{{ $key }}')"
                    @class([ 'relative px-4 py-3 text-sm font-medium transition-colors'
                    , 'text-slate-800 after:absolute after:bottom-0 after:inset-x-0 after:h-0.5 after:bg-primary-500 after:rounded-t-full'=>
                    $activeTab === $key,
                    'text-slate-400 hover:text-slate-600' => $activeTab !== $key,
                    ])>
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- ── Tab Content ── --}}
            <div class="px-6 py-6 space-y-5">

                {{-- ════════ PROFILE TAB ════════ --}}
                @if ($activeTab === 'profile')

                {{-- Success banner --}}
                @if ($profileSuccess)
                <div class="flex items-center gap-2 text-sm text-emerald-700
                            bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 9 17.25l10.5-10.5" />
                    </svg>
                    {{ $profileSuccess }}
                </div>
                @endif

                <form wire:submit="saveProfile" class="space-y-4" novalidate>

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Full Name
                        </label>
                        <input wire:model="full_name" type="text" placeholder="Jane Doe" class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-800
                                   placeholder-slate-300 outline-none transition
                                   @error('full_name') border-rose-400 bg-rose-50
                                   @else border-slate-200 bg-slate-50 focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200
                                   @enderror">
                        @error('full_name')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Address
                        </label>
                        <textarea wire:model="address" rows="2" placeholder="123 Main St, City, Country" class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-800
                                   placeholder-slate-300 outline-none transition resize-none
                                   @error('address') border-rose-400 bg-rose-50
                                   @else border-slate-200 bg-slate-50 focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200
                                   @enderror"></textarea>
                        @error('address')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Phone Number
                        </label>

                        @if (! $showOtpField)
                        <input wire:model="phone_number" type="tel" placeholder="+880 1XXXXXXXXX" class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-800
                                       placeholder-slate-300 outline-none transition
                                       @error('phone_number') border-rose-400 bg-rose-50
                                       @else border-slate-200 bg-slate-50 focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200
                                       @enderror">
                        @error('phone_number')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-400">
                            Changing your number will require OTP verification.
                        </p>

                        @else
                        {{-- OTP verification panel --}}
                        <div class="rounded-xl border border-primary-200 bg-primary-50 p-4 space-y-3">

                            <div class="flex items-start gap-2.5">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-primary-700">Verify your new number</p>
                                    <p class="text-xs text-primary-600 mt-0.5">
                                        OTP sent to <span class="font-bold">{{ $pending_phone }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- OTP input --}}
                            <input wire:model="phone_otp" type="text" inputmode="numeric" maxlength="6"
                                placeholder="• • • • • •"
                                class="w-full rounded-xl border px-4 py-2.5 text-lg font-bold text-center tracking-[0.4em]
                                           outline-none transition bg-white
                                           {{ $phoneOtpError ? 'border-rose-400 bg-rose-50' : 'border-slate-200 focus:border-primary-400 focus:ring-2 focus:ring-primary-100' }}">

                            @if ($phoneOtpError)
                            <p class="text-xs text-rose-500 text-center">{{ $phoneOtpError }}</p>
                            @endif

                            {{-- Verify + Resend + Cancel --}}
                            <div class="flex items-center gap-2">
                                <button wire:click="verifyPhoneOtp" type="button" class="flex-1 py-2 rounded-xl bg-primary-500 text-white text-sm font-semibold
                                               hover:bg-primary-600 active:scale-95 transition-all">
                                    <span wire:loading.remove wire:target="verifyPhoneOtp">Verify OTP</span>
                                    <span wire:loading wire:target="verifyPhoneOtp"
                                        class="inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" d="M12 3a9 9 0 1 0 9 9" />
                                        </svg>
                                        Verifying…
                                    </span>
                                </button>

                                <button wire:click="resendPhoneOtp" type="button" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600
                                               hover:bg-white active:scale-95 transition-all">
                                    Resend
                                </button>

                                <button wire:click="cancelOtp" type="button" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-500
                                               hover:bg-white active:scale-95 transition-all">
                                    Cancel
                                </button>
                            </div>

                        </div>
                        @endif
                    </div>

                    {{-- Save button — hidden while OTP is pending --}}
                    @if (! $showOtpField)
                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold
                                   hover:bg-primary-600 active:scale-95 transition-all
                                   disabled:opacity-60 disabled:cursor-not-allowed" wire:loading.attr="disabled"
                            wire:target="saveProfile">
                            <span wire:loading.remove wire:target="saveProfile">Save Changes</span>
                            <span wire:loading wire:target="saveProfile" class="inline-flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" d="M12 3a9 9 0 1 0 9 9" />
                                </svg>
                                Saving…
                            </span>
                        </button>
                    </div>
                    @endif

                </form>

                {{-- ════════ PASSWORD TAB ════════ --}}
                @elseif ($activeTab === 'password')

                @if ($passwordSuccess)
                <div class="flex items-center gap-2 text-sm text-emerald-700
                            bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 9 17.25l10.5-10.5" />
                    </svg>
                    {{ $passwordSuccess }}
                </div>
                @endif

                <form wire:submit="changePassword" class="space-y-4" novalidate>

                    {{-- Current Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Current Password
                        </label>
                        <div class="relative">
                            <input wire:model="current_password" :type="show ? 'text' : 'password'"
                                placeholder="Enter current password" class="w-full rounded-xl border px-4 py-2.5 pr-11 text-sm text-slate-800
                                       placeholder-slate-300 outline-none transition
                                       @error('current_password') border-rose-400 bg-rose-50
                                       @else border-slate-200 bg-slate-50 focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200
                                       @enderror">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            New Password
                        </label>
                        <div class="relative">
                            <input wire:model="new_password" :type="show ? 'text' : 'password'"
                                placeholder="Min. 6 characters" class="w-full rounded-xl border px-4 py-2.5 pr-11 text-sm text-slate-800
                                       placeholder-slate-300 outline-none transition
                                       @error('new_password') border-rose-400 bg-rose-50
                                       @else border-slate-200 bg-slate-50 focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200
                                       @enderror">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        @error('new_password')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Confirm New Password
                        </label>
                        <input wire:model="new_password_confirmation" type="password"
                            placeholder="Re-enter new password" class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-800
                                   placeholder-slate-300 outline-none transition
                                   @error('new_password_confirmation') border-rose-400 bg-rose-50
                                   @else border-slate-200 bg-slate-50 focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200
                                   @enderror">
                        @error('new_password_confirmation')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed">
                        Password must be at least 6 characters.
                    </p>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold
                                   hover:bg-primary-600 active:scale-95 transition-all
                                   disabled:opacity-60 disabled:cursor-not-allowed" wire:loading.attr="disabled"
                            wire:target="changePassword">
                            <span wire:loading.remove wire:target="changePassword">Update Password</span>
                            <span wire:loading wire:target="changePassword" class="inline-flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" d="M12 3a9 9 0 1 0 9 9" />
                                </svg>
                                Updating…
                            </span>
                        </button>
                    </div>

                </form>
                @endif

            </div>{{-- /tab content --}}
        </div>{{-- /panel --}}
    </div>{{-- /backdrop+modal --}}
    @endif
</div>