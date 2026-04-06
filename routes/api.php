use App\Http\Controllers\ProductionEntryController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\ProductionSupplyController;

Route::apiResource('production-orders', ProductionOrderController::class);
Route::apiResource('production-entries', ProductionEntryController::class);
Route::apiResource('production-supplies', ProductionSupplyController::class)