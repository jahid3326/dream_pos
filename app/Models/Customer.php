<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'phone_number',
        'tax_number',
        'billing_address',
        'status',
        'created_by',
    ];

    // A Customer profile belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created this customer record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
