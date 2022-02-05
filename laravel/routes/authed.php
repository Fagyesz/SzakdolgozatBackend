<?php
//This is the tenant based auth group

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;

Route::group(['prefix' => 'auth'], function (){
    Route::get('logout', [AuthController::class, 'logout'])->name('passport.logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('passport.profile');
});

Route::apiResource('posts', PostController::class)->except('index', 'show');
Route::post('images', [ImageController::class, 'store'])->name('image.store');
Route::apiResource('users', UserController::class);
Route::apiResource('services', ServiceController::class);
Route::get('recommend/{service}/{user}', [AppointmentController::class, 'recommend'])->name('appointments.recommend');
Route::apiResource('appointments', AppointmentController::class);

Route::put('/role/{user}/{role}', [RoleController::class, 'assign'])->name('role.assign');
Route::delete('/role/{user}/{role}', [RoleController::class, 'revoke'])->name('role.revoke');

Route::post('/troll/{user}', [UserController::class, 'setTroll'])->name('troll.set');

