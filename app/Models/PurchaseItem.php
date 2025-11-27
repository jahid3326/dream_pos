<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id']; // <-- THIS IS THE FIX

    /**
     * Get the purchase that owns the item.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the product associated with the purchase item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        // This is a "belongsTo" relationship because one purchase item
        // belongs to one specific product variation.
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
