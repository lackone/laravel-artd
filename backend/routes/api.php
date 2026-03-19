<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('api.common.prefix'),
    'as' => config('api.common.name'),
    'namespace' => config('api.common.namespace'),
    'middleware' => [],
], function () {
    Route::any('/common/upload', 'IndexController@upload')->name('upload');
    Route::any('/common/role_list', 'IndexController@roleList')->name('role_list');
    Route::any('/common/auth_list', 'IndexController@authList')->name('auth_list');
});

Route::group([
    'prefix' => config('api.admin.prefix'),
    'as' => config('api.admin.name'),
    'namespace' => config('api.admin.namespace'),
    'middleware' => [],
], function () {

    Route::any('/admin/login', 'IndexController@login')->name('login');

    Route::group([
        'middleware' => ['ApiTokenValid'],
    ], function () {

        Route::any('/admin/info', 'AdminController@info')->name('admin.info');
        Route::any('/admin/list', 'AdminController@list')->name('admin.list');
        Route::any('/admin/delete', 'AdminController@list')->name('admin.delete');
        Route::any('/admin/save/{admin?}', 'AdminController@save')->name('admin.save');
        Route::any('/admin/set_role/{admin?}', 'AdminController@setRole')->name('admin.set_role');

        Route::any('/role/list', 'AdminRoleController@list')->name('role.list');
        Route::any('/role/save/{role?}', 'AdminRoleController@save')->name('role.save');
        Route::any('/role/delete', 'AdminRoleController@delete')->name('role.delete');

        Route::any('/auth/menu_list', 'AdminAuthController@menuList')->name('auth.menu_list');
    });
});
