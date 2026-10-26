<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any customers.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function viewAny(User $user): bool
    {
        return $user->isOwner() || $user->isManager() || $user->isCashier();
    }

    /**
     * Determine whether the user can view the customer.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function view(User $user, Customer $customer): bool
    {
        return $user->isOwner() || $user->isManager() || $user->isCashier();
    }

    /**
     * Determine whether the user can create customers.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function create(User $user): bool
    {
        return $user->isOwner() || $user->isManager() || $user->isCashier();
    }

    /**
     * Determine whether the user can update the customer.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function update(User $user, Customer $customer): bool
    {
        return $user->isOwner() || $user->isManager() || $user->isCashier();
    }

    /**
     * Determine whether the user can delete the customer.
     * Accessible by: Owner only.
     * Forbidden for: Manager, Cashier.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->isOwner();
    }
}
