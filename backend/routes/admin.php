<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('admin.route.prefix'),
    'as' => config('admin.route.name'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => ['web'],
], function () {

});

Route::fallback(function () {
    abort(404);
});
