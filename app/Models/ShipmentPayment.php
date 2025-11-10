<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentPayment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['payment_date' => 'date'];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
