<?php

use RoleModule\Http\Controller\Authorization\PermissionsController;
use RoleModule\Http\Controller\Authorization\RolePermissionsController;
use RoleModule\Http\Controller\Authorization\RolesController;

Route::group(['middleware' => ['web', 'auth', 'verified']],function () {
    Route::resource('roles', RolesController::class)->except('show')->middleware('permission:roles.manage');

    Route::post('/permissions/save', [RolePermissionsController::class, 'update'])->middleware('permission:permissions.manage')->name('permissions.save');
    Route::resource('permissions', PermissionsController::class)->middleware('permission:permissions.manage');
});
