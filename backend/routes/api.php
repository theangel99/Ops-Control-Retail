<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\CashForecastController;
use App\Http\Controllers\AdminController;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);

// Inventory
Route::get('/inventory', [InventoryController::class, 'index']);

// Purchase Orders
Route::get('/po', [PurchaseOrderController::class, 'index']);
Route::get('/po/{id}', [PurchaseOrderController::class, 'show']);
Route::post('/po/suggest', [PurchaseOrderController::class, 'suggest']);
Route::post('/po/{id}/transition', [PurchaseOrderController::class, 'transition']);

// Cash Forecast
Route::get('/cash/forecast', [CashForecastController::class, 'index']);

// Admin
Route::post('/admin/reset', [AdminController::class, 'reset']);

// Get locations and suppliers for filters
Route::get('/locations', function () {
    return response()->json(['data' => \App\Models\Location::all()]);
});

Route::get('/suppliers', function () {
    return response()->json(['data' => \App\Models\Supplier::all()]);
});
