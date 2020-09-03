<?php

namespace RoleModule\Event\Role;

use RoleModule\Role;

abstract class RoleEvent
{
    /**
     * @var Role
     */
    protected $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }
}