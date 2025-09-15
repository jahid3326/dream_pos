<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePackProductItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_pack_product_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     * This table might not need its own timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the parent pack line item that this constituent part belongs to.
     */
    public function salePackProduct()
    {
        return $this->belongsTo(SalePackProduct::class);
    }

    /**
     * Get the original 'pack_product' entry from the pack definition.
     */
    public function packProduct()
    {
        return $this->belongsTo(PackProduct::class, 'pack_product_id');
    }

    /**
     * Get the original 'pack_product_selected_variation' entry from the pack definition.
     */
    public function packProductSelectedVariation()
    {
        return $this->belongsTo(PackProductSelectedVariation::class, 'pack_product_selected_variation_id');
    }
}
