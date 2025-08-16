<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'measurement', // <-- ESSENTIAL
        'cbm',         // <-- ESSENTIAL
        'weight',      // <-- ESSENTIAL
        'purchase_price',
        'sale_price',
        'margin',
        'tax_id',
        'image',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected $casts = [
        'cbm' => 'float',    // <-- ADD THIS
        'weight' => 'float', // <-- ADD THIS
    ];
}
