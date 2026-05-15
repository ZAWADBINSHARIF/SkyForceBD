<?php

namespace App\Traits;

/**
 * @method Livewire\Features\SupportEvents\HandlesEvents::dispatch dispatch($event, ...$params) { }
 */
trait RequiresCustomerAuth
{
    public function ensureCustomerAuth(): void
    {
        if (! auth('customer')->check()) {
            $this->dispatch('open-auth-modal', mode: 'signin', closeDisable: true);
        }
    }
}
