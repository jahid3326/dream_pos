<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'shipment_date' => 'date',
        'delivery_estimation_date' => 'date',
        'tracking_urls' => 'array',
        'container' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // A shipment can have many payments for its shipping cost
    public function payments()
    {
        return $this->hasMany(ShipmentPayment::class);
    }

    // A shipment can have many documents
    public function documents()
    {
        return $this->hasMany(ShipmentDocument::class);
    }

    // Shipping type relation (nullable)
    public function shippingType()
    {
        return $this->belongsTo(\App\Models\ShippingType::class, 'shipping_type_id');
    }

    // Shipping tax relation (nullable)
    public function shippingTax()
    {
        return $this->belongsTo(\App\Models\ShippingTax::class, 'shipping_tax_id');
    }
}
