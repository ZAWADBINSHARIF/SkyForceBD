<?php

namespace App\Http\Controllers;

use App\Enums\AuthProviderName;
use App\Enums\StoragePath;
use App\Models\AuthProvider;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, AuthProviderName::values(), strict: true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, AuthProviderName::values(), strict: true), 404);

        try {
            /** @var SocialUser $social */
            $social = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect()
                ->route('home')
                ->withErrors(['oauth' => 'Authentication failed. Please try again.']);
        }

        $customer = $this->findOrCreateCustomer($provider, $social);

        Auth::guard('customer')->login($customer, remember: true);

        return redirect()->intended(route('home'));
    }

    // ── Internals ─────────────────────────────────────────────────

    // change findOrCreateCustomer return type to the contract
    private function findOrCreateCustomer(string $provider, SocialUser $social): AuthenticatableContract
    {
        // 1. Already linked — update token and return
        $existing = AuthProvider::query()
            ->where('provider', $provider)
            ->where('provider_uid', (string) $social->getId())
            ->first();

        if ($existing instanceof AuthProvider) {
            $existing->update(['access_token' => $social->token]);

            /** @var AuthenticatableContract $customer */
            $customer = $existing->customer;
            return $customer;
        }

        // 2. Customer exists by email — link the new provider
        $email    = $social->getEmail();
        $customer = $email !== null
            ? Customer::query()->where('email', $email)->first()
            : null;

        // 3. No customer found — create one
        if (! $customer instanceof Customer) {
            $customer = Customer::create([
                'full_name'     => $social->getName() ?? 'Unknown',
                'email'         => $email,
                'avatar_url'    => $this->storeAvatar($social->getAvatar(), $social->getId()),
                'password_hash' => null,
            ]);
        }

        // 4. Link provider to customer
        AuthProvider::create([
            'customer_id'  => $customer->id,
            'provider'     => $provider,
            'provider_uid' => (string) $social->getId(),
            'access_token' => $social->token,
        ]);

        return $customer;
    }

    /**
     * Download the avatar from the provider and store it locally.
     * Returns the storage path, or null if download fails.
     */
    private function storeAvatar(?string $url, string|int $providerId): ?string
    {
        if ($url === null) {
            return null;
        }

        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(10)
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            // Detect extension from Content-Type header
            $contentType = $response->header('Content-Type') ?? 'image/jpeg';
            $extension   = match (true) {
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif')  => 'gif',
                default                            => 'jpg',
            };

            $filename = $providerId . '_' . time() . '.' . $extension;
            $path     = StoragePath::CustomerAvatar->value . '/' . $filename;

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }
}
