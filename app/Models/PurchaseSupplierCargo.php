<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseSupplierCargo extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // This is a placeholder for the pivot model we will create
    public function purchaseSupplier()
    {
        // By Laravel convention, it expects the foreign key to be `purchase_supplier_model_name_id`,
        // which would be 'purchase_supplier_id'. This is correct.
        return $this->belongsTo(PurchaseSupplier::class, 'purchase_supplier_id', 'id');
    }

    public function dimensions()
    {
        return $this->hasMany(CargoDimension::class);
    }
}
