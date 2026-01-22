<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Public Routes
    Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::put('/auth/profile', [AuthController::class, 'updateProfile'])->name('api.auth.profile.update');

        // Users (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });

        // Roles (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('roles', RoleController::class);
            Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('api.roles.permissions');
        });

        // Permissions (Admin only)
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('permissions', PermissionController::class);
        });
    });
});
