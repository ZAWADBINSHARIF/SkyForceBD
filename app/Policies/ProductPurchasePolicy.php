<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProductPurchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPurchasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductPurchase');
    }

    public function view(AuthUser $authUser, ProductPurchase $productPurchase): bool
    {
        return $authUser->can('View:ProductPurchase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductPurchase');
    }

    public function update(AuthUser $authUser, ProductPurchase $productPurchase): bool
    {
        return $authUser->can('Update:ProductPurchase');
    }

    public function delete(AuthUser $authUser, ProductPurchase $productPurchase): bool
    {
        return $authUser->can('Delete:ProductPurchase');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProductPurchase');
    }

    public function restore(AuthUser $authUser, ProductPurchase $productPurchase): bool
    {
        return $authUser->can('Restore:ProductPurchase');
    }

    public function forceDelete(AuthUser $authUser, ProductPurchase $productPurchase): bool
    {
        return $authUser->can('ForceDelete:ProductPurchase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductPurchase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductPurchase');
    }

    public function replicate(AuthUser $authUser, ProductPurchase $productPurchase): bool
    {
        return $authUser->can('Replicate:ProductPurchase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductPurchase');
    }

}