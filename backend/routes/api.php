<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('api.common.prefix'),
    'as' => config('api.common.name'),
    'namespace' => config('api.common.namespace'),
    'middleware' => [],
], function () {


});

Route::group([
    'prefix' => config('api.admin.prefix'),
    'as' => config('api.admin.name'),
    'namespace' => config('api.admin.namespace'),
    'middleware' => [],
], function () {

    Route::any('/index', 'IndexController@index')->name('index');
});
