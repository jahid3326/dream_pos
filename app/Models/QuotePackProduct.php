<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotePackProduct extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quote_pack_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the parent quote that this pack item belongs to.
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
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
        return $this->hasMany(QuotePackProductItem::class);
    }
}
