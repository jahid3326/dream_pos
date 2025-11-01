<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'phone_number',
        'tax_number',
        'billing_address',
        'status',
    ];

    // A Supplier profile belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_supplier')
            ->using(PurchaseSupplier::class)
            ->withPivot(['status', 'status_review', 'status_production', 'ready_date'])
            ->withTimestamps();
    }
}
