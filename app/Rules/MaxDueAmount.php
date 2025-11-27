<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Sale;

class MaxDueAmount implements ValidationRule
{
    protected $sale;
    protected $dueAmount;
    /**
     * Create a new rule instance.
     * We pass the Sale object to the constructor.
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
        // Calculate the due amount once
        $paidAmount = $sale->payments()->sum('amount');
        $this->dueAmount = $sale->grand_total - $paidAmount;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // The validation passes if the submitted amount is less than or equal to the due amount.
        // We add a small tolerance for floating point inaccuracies.
        if (floatval($value) > ($this->dueAmount + 0.001)) {
            $fail("The :attribute cannot be greater than the due amount of $" . number_format($this->dueAmount, 2));
        }
    }
}
