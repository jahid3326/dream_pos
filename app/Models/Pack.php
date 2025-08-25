<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function groups()
    {
        return $this->hasMany(PackGroup::class);
    }
}
