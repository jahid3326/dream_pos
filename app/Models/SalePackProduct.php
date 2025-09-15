<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePackProduct extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_pack_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the parent sale that this pack item belongs to.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the original pack group option that was sold.
     */
    public function packGroupOption()
    {
        return $this->belongsTo(PackGroupOption::class);
    }

    /**
     * Get the list of actual products/variations (the "parts list")
     * that made up this pack at the time of sale.
     */
    public function constituentItems()
    {
        return $this->hasMany(SalePackProductItem::class);
    }
}
