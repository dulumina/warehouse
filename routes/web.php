<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // route units.index
    Route::get('units/datatables', [\App\Http\Controllers\UnitController::class, 'datatables'])
        ->name('units.datatables');

    Route::resource('units', \App\Http\Controllers\UnitController::class);

    // Admin Routes
    // Admin Routes
    Route::get('users/datatables', [\App\Http\Controllers\UserController::class, 'datatables'])->name('users.datatables')->middleware('permission:manage access control');
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware('permission:manage access control');

    Route::get('permissions/datatables', [\App\Http\Controllers\PermissionController::class, 'datatables'])->name('permissions.datatables')->middleware('permission:manage access control');
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class)->middleware('permission:manage access control');

    Route::get('roles/datatables', [\App\Http\Controllers\RoleController::class, 'datatables'])->name('roles.datatables')->middleware('permission:manage access control');
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->middleware('permission:manage access control');

    // Warehouse Management Routes
    Route::group(['middleware' => 'permission:view warehouses'], function () {
        Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class);
        Route::get('/warehouses-datatables', \App\Http\Controllers\WarehouseController::class . '@datatables')->name('warehouses.datatables');
    });

    // Product Management Routes
    Route::group(['middleware' => 'permission:view products'], function () {
        Route::get('products/datatables', [\App\Http\Controllers\ProductController::class, 'datatables'])->name('products.datatables');
        Route::resource('products', \App\Http\Controllers\ProductController::class);

        Route::get('categories/datatables', [\App\Http\Controllers\CategoryController::class, 'datatables'])->name('categories.datatables');
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);

        Route::get('suppliers/datatables', [\App\Http\Controllers\SupplierController::class, 'datatables'])->name('suppliers.datatables');
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    });

    // Inventory Routes
    Route::group(['middleware' => 'permission:view inventory'], function () {
        Route::get('batches/datatables', [\App\Http\Controllers\BatchController::class, 'datatables'])->name('batches.datatables');
        Route::resource('batches', \App\Http\Controllers\BatchController::class);

        Route::get('inventory/datatables', [\App\Http\Controllers\InventoryController::class, 'datatables'])->name('inventory.datatables');
        Route::get('inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/low-stock', [\App\Http\Controllers\InventoryController::class, 'lowStock'])->name('inventory.low-stock');
        Route::get('inventory/expiring', [\App\Http\Controllers\InventoryController::class, 'expiring'])->name('inventory.expiring');
    });

    // Stock Transaction Routes
    Route::group(['middleware' => 'permission:create stock in|create stock out|create stock transfer|create stock adjustment'], function () {
        Route::get('stock-ins/datatables', [\App\Http\Controllers\StockInController::class, 'datatables'])->name('stock-ins.datatables');
        Route::resource('stock-ins', \App\Http\Controllers\StockInController::class);
        Route::post('stock-ins/{stockIn}/pending', \App\Http\Controllers\StockInController::class . '@pending')->name('stock-ins.pending');
        Route::post('stock-ins/{stockIn}/approve', \App\Http\Controllers\StockInController::class . '@approve')->name('stock-ins.approve');
        Route::post('stock-ins/{stockIn}/reject', \App\Http\Controllers\StockInController::class . '@reject')->name('stock-ins.reject');

        Route::get('stock-outs/datatables', [\App\Http\Controllers\StockOutController::class, 'datatables'])->name('stock-outs.datatables');
        Route::resource('stock-outs', \App\Http\Controllers\StockOutController::class);
        Route::post('stock-outs/{stockOut}/pending', \App\Http\Controllers\StockOutController::class . '@pending')->name('stock-outs.pending');
        Route::post('stock-outs/{stockOut}/approve', \App\Http\Controllers\StockOutController::class . '@approve')->name('stock-outs.approve');
        Route::post('stock-outs/{stockOut}/reject', \App\Http\Controllers\StockOutController::class . '@reject')->name('stock-outs.reject');

        Route::get('stock-transfers/datatables', [\App\Http\Controllers\StockTransferController::class, 'datatables'])->name('stock-transfers.datatables');
        Route::resource('stock-transfers', \App\Http\Controllers\StockTransferController::class);
        Route::post('stock-transfers/{stockTransfer}/send', \App\Http\Controllers\StockTransferController::class . '@send')->name('stock-transfers.send');
        Route::post('stock-transfers/{stockTransfer}/receive', \App\Http\Controllers\StockTransferController::class . '@receive')->name('stock-transfers.receive');

        Route::get('stock-adjustments/datatables', [\App\Http\Controllers\StockAdjustmentController::class, 'datatables'])->name('stock-adjustments.datatables');
        Route::resource('stock-adjustments', \App\Http\Controllers\StockAdjustmentController::class);
        Route::post('stock-adjustments/{stockAdjustment}/approve', \App\Http\Controllers\StockAdjustmentController::class . '@approve')->name('stock-adjustments.approve');
        Route::post('stock-adjustments/{stockAdjustment}/reject', \App\Http\Controllers\StockAdjustmentController::class . '@reject')->name('stock-adjustments.reject');
    });

    // Approval Routes
    Route::group(['middleware' => 'permission:approve stock in|approve stock out|approve stock transfer|approve stock adjustment', 'prefix' => 'approvals', 'as' => 'approvals.'], function () {
        Route::get('stock-ins', \App\Http\Controllers\ApprovalController::class . '@stockIns')->name('stock-ins');
        Route::get('stock-outs', \App\Http\Controllers\ApprovalController::class . '@stockOuts')->name('stock-outs');
        Route::get('stock-transfers', \App\Http\Controllers\ApprovalController::class . '@stockTransfers')->name('stock-transfers');
        Route::get('stock-adjustments', \App\Http\Controllers\ApprovalController::class . '@stockAdjustments')->name('stock-adjustments');
    });

    // Reports Routes
    Route::group(['middleware' => 'permission:view stock reports|view movement reports|view valuation reports', 'prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/', \App\Http\Controllers\ReportController::class . '@index')->name('index');
        Route::get('stock', \App\Http\Controllers\ReportController::class . '@stock')->name('stock');
        Route::get('movements', \App\Http\Controllers\ReportController::class . '@movements')->name('movements');
        Route::get('valuation', \App\Http\Controllers\ReportController::class . '@valuation')->name('valuation');
    });
});

require __DIR__.'/auth.php';
Route::view('/modern-test', 'modern-test');
