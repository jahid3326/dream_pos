<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'route', 'icon', 'parent_id', 'order'];

    public function children()
    {
        return $this->hasMany(NavItem::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(NavItem::class, 'parent_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_nav_item');
    }
}
