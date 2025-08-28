<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackGroupOption extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function packGroup()
    {
        return $this->belongsTo(PackGroup::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'pack_product')
            ->withPivot('id', 'position')
            ->orderBy('pivot_position', 'asc');
    }
}
