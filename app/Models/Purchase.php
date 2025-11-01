<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['purchase_date' => 'date', 'ready_date' => 'date'];

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'purchase_supplier')
            ->using(PurchaseSupplier::class)
            ->withPivot(['status', 'status_review', 'status_production', 'ready_date'])
            ->withTimestamps();
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function documents()
    {
        return $this->hasMany(PurchaseDocument::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}
