<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    /**
     * Determine whether the user can view any items.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function viewAny(User $user): bool
    {
        return $user->isOwner() || $user->isManager() || $user->isCashier();
    }

    /**
     * Determine whether the user can view the item.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function view(User $user, Item $item): bool
    {
        return $user->isOwner() || $user->isManager() || $user->isCashier();
    }

    /**
     * Determine whether the user can create items.
     * Accessible by: Owner, Manager.
     * Forbidden for: Cashier.
     */
    public function create(User $user): bool
    {
        return $user->isOwner() || $user->isManager();
    }

    /**
     * Determine whether the user can update the item.
     * Accessible by: Owner, Manager, Cashier (e.g., updating stock during checkout).
     */
    public function update(User $user, Item $item): bool
    {
        return $user->isOwner() || $user->isManager() || $user->isCashier();
    }

    /**
     * Determine whether the user can delete the item.
     * Accessible by: Owner only.
     * Forbidden for: Manager, Cashier.
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->isOwner();
    }
}
