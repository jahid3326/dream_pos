<?php

namespace App\Policies;

use App\Models\SalePayment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalePaymentPolicy
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
    public function view(User $user, SalePayment $salePayment): bool
    {
        //
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
    public function update(User $user, SalePayment $salePayment): bool
    {
        // Rule 1: A Super Admin can update any payment.
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can update a payment if they own the parent sale.
        return $user->id === $salePayment->sale->order_taken_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SalePayment $salePayment): bool
    {
        // Rule 1: A Super Admin can update any payment.
        if ($user->role && $user->role->name == 'Super Admin') {
            return true;
        }

        // Rule 2: A user can update a payment if they own the parent sale.
        return $user->id === $salePayment->sale->order_taken_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SalePayment $salePayment): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SalePayment $salePayment): bool
    {
        //
    }
}
