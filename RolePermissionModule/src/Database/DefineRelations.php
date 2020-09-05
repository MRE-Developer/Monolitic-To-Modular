<?php


namespace RoleModule\Database;


use RoleModule\Role;
use Vanguard\User;

class DefineRelations {
    public static function roleRelations() {

        User::belongs_to("role", Role::class, "role_id");
        User::defineNewMethod("hasRole", function ($role) {
            return $this->role->name === $role;
        });
        User::defineNewMethod("setRole", function ($role) {
            return $this->forceFill([
                'role_id' => $role instanceof Role ? $role->id : $role
            ])->save();
        });
    }

    public static function permissionRelations() {
        User::defineNewMethod("hasPermission", function ($permission, $allRequired = true) {
            $permission = (array)$permission;

            return $allRequired
                ? $this->hasAllPermissions($permission)
                : $this->hasAtLeastOnePermission($permission);
        });
        User::defineNewMethod("hasAllPermissions", function (array $permissions) {
            $availablePermissions = $this->role->cachedPermissions()->pluck('name')->toArray();

            foreach ($permissions as $perm) {
                if (!in_array($perm, $availablePermissions, true)) {
                    return false;
                }
            }

            return true;
        });
        User::defineNewMethod("hasAtLeastOnePermission", function (array $permissions) {
            $availablePermissions = $this->role->cachedPermissions()->pluck('name')->toArray();

            foreach ($permissions as $perm) {
                if (in_array($perm, $availablePermissions, true)) {
                    return true;
                }
            }

            return false;
        });
    }
}
