<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        // Rule 1: A Super Admin can view any customer.
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can view a customer if they created it.
        return $user->id === $customer->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        // Rule 1: A Super Admin can view any customer.
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can delete a customer if they created it.
        return $user->id === $customer->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Rule 1: A Super Admin can view any customer.
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can delete a customer if they created it.
        return $user->id === $customer->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Customer $customer): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        //
    }
}
