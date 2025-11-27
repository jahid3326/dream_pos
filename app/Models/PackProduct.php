<?php

namespace App\Models;

// --- THIS IS THE KEY CHANGE ---
// It must extend 'Pivot', not 'Model'.
use Illuminate\Database\Eloquent\Relations\Pivot;

class PackProduct extends Pivot
{
    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'pack_product';

    /**
     * Indicates if the model should be timestamped.
     * Your pack_product table has timestamps, so this should be true.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the parent Product model for this pivot entry.
     * (This relationship is not strictly needed here but can be useful)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the PackGroupOption model for this pivot entry.
     * (Also not strictly needed but can be useful)
     */
    public function packGroupOption()
    {
        return $this->belongsTo(PackGroupOption::class);
    }

    /**
     * The variations that have been selected for this specific pack_product entry.
     */
    /**
     * The variations that have been selected for this specific pack_product entry.
     */
    public function selectedVariations()
    {
        return $this->hasMany(PackProductSelectedVariation::class, 'pack_product_id');
    }
}
