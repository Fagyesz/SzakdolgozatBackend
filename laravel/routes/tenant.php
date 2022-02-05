<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('passport.login');
    Route::post('register', [AuthController::class, 'register'])->name('passport.register');
});

Route::get('echo', function () {
    return 'OK';
});

Route::apiResource('posts', PostController::class)->only('index', 'show');
Route::get('role', [RoleController::class, 'index'])->name('roles.index');
Route::get('server/{user}', [ServiceController::class, 'getServicesFor'])->name('servers.show');
