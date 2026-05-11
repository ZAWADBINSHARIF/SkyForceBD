<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    // Modal state
    public bool $show = false;
    public string $activeTab = 'profile';

    // Profile fields
    public string $full_name = '';
    public string $address = '';
    public string $phone_number = '';
    public string $email = '';

    // Password fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Feedback messages
    public string $profileSuccess = '';
    public string $passwordSuccess = '';

    public function mount(): void
    {
        $user = Auth::guard('customer')->user();
        $this->full_name     = $user->full_name     ?? $user->name ?? '';
        $this->address       = $user->address       ?? '';
        $this->phone_number  = $user->phone_number  ?? '';
        $this->email         = $user->email  ?? '';
    }

    #[On('open-profile-modal')]
    public function openModal(): void
    {
        $this->show = true;
    }

    #[On('close-profile-modal')]
    public function closeModal(): void
    {
        $this->show = false;
        $this->resetFeedback();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetFeedback();
        $this->resetErrorBag();
    }

    public function saveProfile(): void
    {
        $this->validate([
            'full_name'    => ['required', 'string', 'max:100'],
            'address'      => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'regex:/^[0-9\+\-\(\)\s]{7,20}$/'],
        ], [
            'full_name.required'    => 'Full name is required.',
            'phone_number.regex'    => 'Enter a valid phone number.',
        ]);

        $user = Auth::guard('customer')->user();
        $user->update([
            'full_name'    => $this->full_name,
            'address'      => $this->address,
            'phone_number' => $this->phone_number,
        ]);

        $this->profileSuccess = 'Profile updated successfully.';
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password'          => ['required'],
            'new_password'              => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
            'new_password_confirmation' => ['required'],
        ], [
            'new_password.confirmed' => 'Passwords do not match.',
        ]);

        if (! Hash::check($this->current_password, Auth::guard('customer')->user()->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        Auth::guard('customer')->user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->passwordSuccess = 'Password changed successfully.';
    }

    private function resetFeedback(): void
    {
        $this->profileSuccess = '';
        $this->passwordSuccess = '';
    }
};
?>

<div>
    {{-- ── Backdrop + Modal ───────────────────────────────────────────── --}}
    @if ($show)
    <div x-data x-on:keydown.escape.window="$dispatch('close-profile-modal')"
        class="fixed inset-0 z-40 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$dispatch('close-profile-modal')"></div>

        {{-- Panel --}}
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl
                    ring-1 ring-slate-200 overflow-hidden" role="dialog" aria-modal="true" aria-label="Edit Profile">

            {{-- ── Header ────────────────────────────────────────────── --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-bold select-none">
                        {{ strtoupper(substr(Auth::guard('customer')->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 leading-tight">Account Settings</p>
                        <p class="text-xs text-slate-400">{{ Auth::guard('customer')->user()->email ?? '' }}</p>
                    </div>
                </div>
                <button wire:click="$dispatch('close-profile-modal')"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- ── Tab Bar ────────────────────────────────────────────── --}}
            <div class="flex border-b border-slate-100 px-6 gap-1">
                @foreach ([['profile','Profile'], ['password','Password']] as [$key, $label])
                <button wire:click="setTab('{{ $key }}')"
                    @class([ 'relative px-4 py-3 text-sm font-medium transition-colors'
                    , 'text-slate-800 after:absolute after:bottom-0 after:inset-x-0 after:h-0.5 after:bg-primary-500 after:rounded-t-full'=>
                    $activeTab === $key,
                    'text-slate-400 hover:text-slate-600'
                    => $activeTab !== $key,
                    ])>
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- ── Tab Content ─────────────────────────────────────────── --}}
            <div class="px-6 py-6 space-y-5">

                {{-- ════════ PROFILE TAB ════════ --}}
                @if ($activeTab === 'profile')

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
                        <input wire:model="full_name" type="text" placeholder="Jane Doe"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-800
                                          placeholder-slate-300 outline-none transition
                                          @error('full_name') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50
                                          focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200 @enderror">
                        @error('full_name')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Address
                        </label>
                        <textarea wire:model="address" rows="2" placeholder="123 Main St, City, Country"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-800
                                             placeholder-slate-300 outline-none transition resize-none
                                             @error('address') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50
                                             focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200 @enderror"></textarea>
                        @error('address')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Phone Number
                        </label>
                        <input wire:model="phone_number" type="tel" placeholder="+1 (555) 000-0000"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-800
                                          placeholder-slate-300 outline-none transition
                                          @error('phone_number') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50
                                          focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200 @enderror">
                        @error('phone_number')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold
                                           hover:bg-primary-600 active:scale-95 transition-all
                                           disabled:opacity-60 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled" wire:target="saveProfile">
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
                                placeholder="Enter current password"
                                class="w-full rounded-xl border px-4 py-2.5 pr-11 text-sm text-slate-800
                                              placeholder-slate-300 outline-none transition
                                              @error('current_password') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50
                                              focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200 @enderror">
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
                                placeholder="Min. 8 chars, mixed case + number"
                                class="w-full rounded-xl border px-4 py-2.5 pr-11 text-sm text-slate-800
                                              placeholder-slate-300 outline-none transition
                                              @error('new_password') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50
                                              focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200 @enderror">
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
                            placeholder="Re-enter new password"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-800
                                          placeholder-slate-300 outline-none transition
                                          @error('new_password_confirmation') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50
                                          focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-200 @enderror">
                        @error('new_password_confirmation')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Hint --}}
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Password must be at least 8 characters and include uppercase, lowercase, and a number.
                    </p>

                    {{-- Actions --}}
                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold
                                           hover:bg-primary-600 active:scale-95 transition-all
                                           disabled:opacity-60 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled" wire:target="changePassword">
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