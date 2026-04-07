<?php

use App\Http\Controllers\ProductionEntryController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\ProductionSupplyController;
use Illuminate\Support\Facades\Route;

Route::apiResource('production-orders', ProductionOrderController::class);
Route::apiResource('production-entries', ProductionEntryController::class);
Route::apiResource('production-supplies', ProductionSupplyController::class);