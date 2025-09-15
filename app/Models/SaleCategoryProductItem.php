<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleCategoryProductItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_category_product_items';

    /**
     * The attributes that are mass assignable.
     * Using guarded is a convenient way to make all attributes fillable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the parent sale that this item belongs to.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the original product model.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the original product variation model (if it was a variation).
     */
    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
