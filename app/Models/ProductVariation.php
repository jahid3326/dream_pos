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
        'measurement',
        'cbm',
        'weight',
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

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    protected $casts = [
        'cbm' => 'float',
        'weight' => 'float',
        'margin' => 'float',
    ];
}
