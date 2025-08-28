<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackProduct extends Model
{
    use HasFactory;

    protected $table = 'pack_product';

    public function selectedVariations()
    {
        // This relationship needs to be defined to work
        return $this->belongsToMany(ProductVariation::class, 'pack_product_selected_variations');
    }
}
