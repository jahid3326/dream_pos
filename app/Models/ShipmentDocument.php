<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'document_type',
        'original_name',
        'stored_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Get the full URL to the document file
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
