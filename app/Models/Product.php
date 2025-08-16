<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Allow mass assignment for all fields

    protected static function boot() {
        parent::boot();
        static::creating(fn($product) => $product->slug = Str::slug($product->name));
    }

    public function category() { 
        return $this->belongsTo(Category::class); 
    }

    public function supplier() { 
        return $this->belongsTo(Supplier::class); 
    }

    public function variations() { 
        return $this->hasMany(ProductVariation::class); 
    }

}
