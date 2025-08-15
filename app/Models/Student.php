<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'roll_number',
        'class_name',
        'parent_name',
        'phone_number',
        'address'
    ];

    // A student belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
