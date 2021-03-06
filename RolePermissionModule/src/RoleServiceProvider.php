<?php

namespace RoleModule;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RoleModule\Database\DefineRelations;
use RoleModule\Database\seeds\PermissionsSeeder;
use RoleModule\Database\seeds\RolesSeeder;
use RoleModule\Http\Middleware\CheckPermissions;
use RoleModule\Http\Middleware\CheckRole;
use RoleModule\Database\Repositories\Permission\EloquentPermission;
use RoleModule\Database\Repositories\Permission\PermissionRepository;
use RoleModule\Database\Repositories\Role\EloquentRole;
use RoleModule\Database\Repositories\Role\RoleRepository;
use Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vanguard\Providers\VanguardServiceProvider;
use Vanguard\User;

class RoleServiceProvider extends ServiceProvider {
    public function register() {

        array_splice(VanguardServiceProvider::$plugs, 3, 0, [
            \RoleModule\Plugins\RolesAndPermissions::class
        ]);
        DefineRelations::roleRelations();
        DefineRelations::permissionRelations();
        app("router")->aliasMiddleware('role', CheckRole::class);
        app("router")->aliasMiddleware('permission', CheckPermissions::class);

        $this->app->singleton(RoleRepository::class, EloquentRole::class);
        $this->app->singleton(PermissionRepository::class, EloquentPermission::class);
        $this->defineSeeder();
    }

    public function boot() {

        $this->defineListener();
        $this->bindRole();
        $this->defineDirective();

        User::saving(function ($user){
            $roles = app(RoleRepository::class);
            $user->role_id = request("role_id") ?: $roles->findByName("User")->id;
        });

        $this->loadViewsFrom(__DIR__ . "/views", "Role");
        app(Factory::class)->load(__DIR__ . "/Database/factories");
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/roles_routes.php');
    }

    private function bindRole() {
        Route::bind('role', function ($id) {
            if ($object = app(RoleRepository::class)->find($id)) {
                return $object;
            }

            throw new NotFoundHttpException("Resource not found.");
        });
    }

    private function defineDirective() {
        \Blade::directive('role', function ($expression) {
            return "<?php if (\\Auth::user()->hasRole({$expression})) : ?>";
        });

        \Blade::directive('endrole', function ($expression) {
            return "<?php endif; ?>";
        });

        \Blade::directive('permission', function ($expression) {
            return "<?php if (\\Auth::user()->hasPermission({$expression})) : ?>";
        });

        \Blade::directive('endpermission', function ($expression) {
            return "<?php endif; ?>";
        });
    }

    private function defineSeeder() {
        \DatabaseSeeder::addSeeder(RolesSeeder::class);
        \DatabaseSeeder::addSeeder(PermissionsSeeder::class);
    }

    public function defineListener() {
        Event::listen("seeding.users", function () {
            $admin = Role::where('name', 'Admin')->first();
            return ["role_id" => $admin->id];
        });
    }
}
