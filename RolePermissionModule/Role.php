<?php

namespace RoleModule;

use Illuminate\Database\Eloquent\Model;
use RoleModule\Database\AuthorizationRoleTrait;

class Role extends Model
{
    use AuthorizationRoleTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    protected $casts = [
        'removable' => 'boolean'
    ];

    protected $fillable = ['name', 'display_name', 'description'];

    public function users()
    {
        return $this->hasMany(\Vanguard\User::class, 'role_id');
    }
}
