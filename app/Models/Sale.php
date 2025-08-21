<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['sales_date' => 'datetime'];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function orderTax()
    {
        return $this->belongsTo(Tax::class, 'order_tax_id');
    }
}
