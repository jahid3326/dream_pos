<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalePolicy
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
    public function view(User $user, Sale $sale): bool
    {
        // Rule 1: A Super Admin can view any sale.
        // Use your role checking logic here (e.g., Spatie's hasRole or a relationship check)
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can view a sale if they are the one who took the order.
        return $user->id === $sale->order_taken_by;
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
    public function update(User $user, Sale $sale): bool
    {
        // Rule 1: A Super Admin can view any sale.
        // Use your role checking logic here (e.g., Spatie's hasRole or a relationship check)
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can view a sale if they are the one who took the order.
        return $user->id === $sale->order_taken_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sale $sale): bool
    {
        // Rule 1: A Super Admin can view any sale.
        // Use your role checking logic here (e.g., Spatie's hasRole or a relationship check)
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can view a sale if they are the one who took the order.
        return $user->id === $sale->order_taken_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sale $sale): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sale $sale): bool
    {
        //
    }
}
