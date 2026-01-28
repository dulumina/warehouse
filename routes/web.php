<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // route units.index
    Route::get('units/datatables', [\App\Http\Controllers\UnitController::class, 'datatables'])
        ->name('units.datatables');

    Route::resource('units', \App\Http\Controllers\UnitController::class);

    // Admin Routes
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware('role:admin');
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class)->middleware('role:admin');
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->middleware('role:admin');

    // Warehouse Management Routes
    Route::group(['middleware' => 'permission:view warehouses'], function () {
        Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class);
        Route::get('/warehouses-datatables', \App\Http\Controllers\WarehouseController::class . '@datatables')->name('warehouses.datatables');
    });

    // Product Management Routes
    Route::group(['middleware' => 'permission:view products'], function () {
        Route::resource('products', \App\Http\Controllers\ProductController::class);
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    });

    // Inventory Routes
    Route::group(['middleware' => 'permission:view inventory'], function () {
        Route::get('/inventory', \App\Http\Controllers\InventoryController::class . '@index')->name('inventory.index');
        Route::get('/inventory/low-stock', \App\Http\Controllers\InventoryController::class . '@lowStock')->name('inventory.low-stock');
        Route::get('/inventory/expiring', \App\Http\Controllers\InventoryController::class . '@expiring')->name('inventory.expiring');
        Route::resource('batches', \App\Http\Controllers\BatchController::class)->only(['index', 'show']);
    });

    // Stock Transaction Routes
    Route::group(['middleware' => 'permission:create stock in|create stock out|create stock transfer|create stock adjustment'], function () {
        Route::resource('stock-ins', \App\Http\Controllers\StockInController::class);
        Route::post('stock-ins/{stockIn}/pending', \App\Http\Controllers\StockInController::class . '@pending')->name('stock-ins.pending');
        Route::post('stock-ins/{stockIn}/approve', \App\Http\Controllers\StockInController::class . '@approve')->name('stock-ins.approve');
        Route::post('stock-ins/{stockIn}/reject', \App\Http\Controllers\StockInController::class . '@reject')->name('stock-ins.reject');

        Route::resource('stock-outs', \App\Http\Controllers\StockOutController::class);
        Route::post('stock-outs/{stockOut}/pending', \App\Http\Controllers\StockOutController::class . '@pending')->name('stock-outs.pending');
        Route::post('stock-outs/{stockOut}/approve', \App\Http\Controllers\StockOutController::class . '@approve')->name('stock-outs.approve');
        Route::post('stock-outs/{stockOut}/reject', \App\Http\Controllers\StockOutController::class . '@reject')->name('stock-outs.reject');

        Route::resource('stock-transfers', \App\Http\Controllers\StockTransferController::class);
        Route::post('stock-transfers/{stockTransfer}/send', \App\Http\Controllers\StockTransferController::class . '@send')->name('stock-transfers.send');
        Route::post('stock-transfers/{stockTransfer}/receive', \App\Http\Controllers\StockTransferController::class . '@receive')->name('stock-transfers.receive');

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
        Route::get('stock', \App\Http\Controllers\ReportController::class . '@stock')->name('stock');
        Route::get('movements', \App\Http\Controllers\ReportController::class . '@movements')->name('movements');
        Route::get('valuation', \App\Http\Controllers\ReportController::class . '@valuation')->name('valuation');
    });
});

require __DIR__.'/auth.php';
Route::view('/modern-test', 'modern-test');
