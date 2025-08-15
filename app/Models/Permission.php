<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'action_permissions'; // <-- ADD THIS LINE

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id', 
        'model_name', 
        'can_create', 
        'can_read', 
        'can_show',
        'can_update', 
        'can_delete',
    ];

    /**
     * Get the role that owns the permission.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}