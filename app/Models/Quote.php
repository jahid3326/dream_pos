<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['quote_date' => 'datetime'];

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
        return $this->belongsTo(User::class, 'created_by');
    }
    public function categoryItems()
    {
        return $this->hasMany(QuoteCategoryProductItem::class);
    }
    public function packItems()
    {
        return $this->hasMany(QuotePackProduct::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }
}
