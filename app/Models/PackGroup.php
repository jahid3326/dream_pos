<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackGroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }
    public function options()
    {
        return $this->hasMany(PackGroupOption::class);
    }
}
