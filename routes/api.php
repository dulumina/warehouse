<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\StockInController;
use App\Http\Controllers\Api\StockOutController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\StockAdjustmentController;
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

        // Warehouse Management
        Route::apiResource('warehouses', WarehouseController::class);
        Route::get('/warehouses/{warehouse}/locations', [WarehouseController::class, 'getLocations'])->name('api.warehouses.locations');

        // Product Management
        Route::apiResource('products', ProductController::class);
        Route::get('/products/{product}/inventory', [ProductController::class, 'inventory'])->name('api.products.inventory');

        // Inventory Management
        Route::prefix('inventory')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('api.inventory.index');
            Route::get('/warehouse/{warehouse}', [InventoryController::class, 'warehouseInventory'])->name('api.inventory.warehouse');
            Route::get('/product/{product}', [InventoryController::class, 'productInventory'])->name('api.inventory.product');
            Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('api.inventory.low-stock');
            Route::get('/expiring', [InventoryController::class, 'expiringItems'])->name('api.inventory.expiring');
        });

        // Stock Transactions
        Route::prefix('stock-ins')->group(function () {
            Route::get('/', [StockInController::class, 'index'])->name('api.stock-ins.index');
            Route::post('/', [StockInController::class, 'store'])->name('api.stock-ins.store');
            Route::get('/{stockIn}', [StockInController::class, 'show'])->name('api.stock-ins.show');
            Route::delete('/{stockIn}', [StockInController::class, 'destroy'])->name('api.stock-ins.destroy');
            Route::post('/{stockIn}/pending', [StockInController::class, 'markAsPending'])->name('api.stock-ins.pending');
            Route::post('/{stockIn}/approve', [StockInController::class, 'approve'])->name('api.stock-ins.approve');
            Route::post('/{stockIn}/reject', [StockInController::class, 'reject'])->name('api.stock-ins.reject');
        });

        Route::prefix('stock-outs')->group(function () {
            Route::get('/', [StockOutController::class, 'index'])->name('api.stock-outs.index');
            Route::post('/', [StockOutController::class, 'store'])->name('api.stock-outs.store');
            Route::get('/{stockOut}', [StockOutController::class, 'show'])->name('api.stock-outs.show');
            Route::delete('/{stockOut}', [StockOutController::class, 'destroy'])->name('api.stock-outs.destroy');
            Route::post('/{stockOut}/pending', [StockOutController::class, 'markAsPending'])->name('api.stock-outs.pending');
            Route::post('/{stockOut}/approve', [StockOutController::class, 'approve'])->name('api.stock-outs.approve');
            Route::post('/{stockOut}/reject', [StockOutController::class, 'reject'])->name('api.stock-outs.reject');
        });

        Route::prefix('stock-transfers')->group(function () {
            Route::get('/', [StockTransferController::class, 'index'])->name('api.stock-transfers.index');
            Route::post('/', [StockTransferController::class, 'store'])->name('api.stock-transfers.store');
            Route::get('/{transfer}', [StockTransferController::class, 'show'])->name('api.stock-transfers.show');
            Route::delete('/{transfer}', [StockTransferController::class, 'destroy'])->name('api.stock-transfers.destroy');
            Route::post('/{transfer}/send', [StockTransferController::class, 'send'])->name('api.stock-transfers.send');
            Route::post('/{transfer}/receive', [StockTransferController::class, 'receive'])->name('api.stock-transfers.receive');
        });

        Route::prefix('stock-adjustments')->group(function () {
            Route::get('/', [StockAdjustmentController::class, 'index'])->name('api.stock-adjustments.index');
            Route::post('/', [StockAdjustmentController::class, 'store'])->name('api.stock-adjustments.store');
            Route::get('/{adjustment}', [StockAdjustmentController::class, 'show'])->name('api.stock-adjustments.show');
            Route::delete('/{adjustment}', [StockAdjustmentController::class, 'destroy'])->name('api.stock-adjustments.destroy');
            Route::post('/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('api.stock-adjustments.approve');
            Route::post('/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])->name('api.stock-adjustments.reject');
        });
    });
});
