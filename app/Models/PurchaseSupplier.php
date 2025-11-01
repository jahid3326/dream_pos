<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PurchaseSupplier extends Pivot
{
    // Define the table name explicitly
    protected $table = 'purchase_supplier';

    // A cargo record belongs to this pivot record
    public function cargo()
    {
        // The problem is here.
        return $this->hasOne(PurchaseSupplierCargo::class, 'purchase_supplier_id', 'id');
    }
}
