<?php

namespace Vanguard\Http\Controllers\Api\Authorization;

use Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use RoleModule\Event\Role\PermissionsUpdated;
use Vanguard\Http\Controllers\Api\ApiController;
use RoleModule\Http\Requests\Role\CreateRoleRequest;
use RoleModule\Http\Requests\Role\RemoveRoleRequest;
use RoleModule\Http\Requests\Role\UpdateRolePermissionsRequest;
use RoleModule\Http\Requests\Role\UpdateRoleRequest;
use RoleModule\Database\Repositories\Role\RoleRepository;
use Vanguard\Repositories\User\UserRepository;
use RoleModule\Role;
use RoleModule\Transformer\PermissionTransformer;
use RoleModule\Transformer\RoleTransformer;

/**
 * Class RolePermissionsController
 * @package Vanguard\Http\Controllers\Api
 */
class RolePermissionsController extends ApiController
{
    /**
     * @var RoleRepository
     */
    private $roles;

    public function __construct(RoleRepository $roles)
    {
        $this->roles = $roles;
        $this->middleware('auth');
        $this->middleware('permission:permissions.manage');
    }

    public function show(Role $role)
    {
        return $this->respondWithCollection(
            $role->cachedPermissions(),
            new PermissionTransformer
        );
    }

    /**
     * Update specified role.
     * @param Role $role
     * @param UpdateRolePermissionsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Role $role, UpdateRolePermissionsRequest $request)
    {
        $this->roles->updatePermissions(
            $role->id,
            $request->permissions
        );

        event(new PermissionsUpdated);

        return $this->respondWithCollection(
            $role->cachedPermissions(),
            new PermissionTransformer
        );
    }
}
