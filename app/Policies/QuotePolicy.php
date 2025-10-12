<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuotePolicy
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
    public function view(User $user, Quote $quote): bool
    {
        // Rule 1: Super Admin can view any quote.
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can view a quote if they created it.
        return $user->id === $quote->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool {}

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quote $quote): bool
    {
        // Rule 1: Super Admin can view any quote.
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can view a quote if they created it.
        return $user->id === $quote->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quote $quote): bool
    {
        // Rule 1: Super Admin can view any quote.
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can view a quote if they created it.
        return $user->id === $quote->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Quote $quote): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Quote $quote): bool
    {
        //
    }
}
