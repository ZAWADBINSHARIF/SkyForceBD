<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AdditionalPage;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdditionalPagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AdditionalPage');
    }

    public function view(AuthUser $authUser, AdditionalPage $additionalPage): bool
    {
        return $authUser->can('View:AdditionalPage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AdditionalPage');
    }

    public function update(AuthUser $authUser, AdditionalPage $additionalPage): bool
    {
        return $authUser->can('Update:AdditionalPage');
    }

    public function delete(AuthUser $authUser, AdditionalPage $additionalPage): bool
    {
        return $authUser->can('Delete:AdditionalPage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AdditionalPage');
    }

    public function restore(AuthUser $authUser, AdditionalPage $additionalPage): bool
    {
        return $authUser->can('Restore:AdditionalPage');
    }

    public function forceDelete(AuthUser $authUser, AdditionalPage $additionalPage): bool
    {
        return $authUser->can('ForceDelete:AdditionalPage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AdditionalPage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AdditionalPage');
    }

    public function replicate(AuthUser $authUser, AdditionalPage $additionalPage): bool
    {
        return $authUser->can('Replicate:AdditionalPage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AdditionalPage');
    }

}