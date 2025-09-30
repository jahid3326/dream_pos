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
    public function orderTax()
    {
        return $this->belongsTo(Tax::class, 'order_tax_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'order_taken_by');
    }
    public function categoryItems()
    {
        return $this->hasMany(SaleCategoryProductItem::class);
    }
    public function packItems()
    {
        return $this->hasMany(SalePackProduct::class);
    }
    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
