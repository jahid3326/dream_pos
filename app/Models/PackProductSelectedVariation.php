<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackProductSelectedVariation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pack_product_selected_variations';

    /**
     * Indicates if the model should be timestamped.
     * This table is a simple pivot, so timestamps are not needed.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * Using guarded is a convenient way to make all attributes fillable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the parent pack_product entry that this selection belongs to.
     */
    public function packProduct()
    {
        return $this->belongsTo(PackProduct::class);
    }

    /**
     * Get the parent product associated with this selection.
     * This is useful for getting the product's name, supplier, etc.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the specific product variation associated with this selection (if any).
     */
    public function variation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
