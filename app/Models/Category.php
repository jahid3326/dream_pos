<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'logo', 'parent_id'];

    // Automatically create a slug from the name
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    // A category can have many children
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // A category belongs to one parent
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    // A category can have many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
