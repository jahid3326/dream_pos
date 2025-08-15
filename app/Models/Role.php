<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function navItems()
    {
        return $this->belongsToMany(NavItem::class, 'role_nav_item');
    }
    
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
