<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDocumentController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\FactoryManagerController;
use App\Http\Controllers\EngineeringProjectController;
use App\Http\Controllers\ProjectUpdateController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionEntryController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\ProductionSupplyController;
use App\Http\Controllers\SalesContractController;
use App\Http\Controllers\ArchitectController;
use App\Http\Controllers\ArchitectTaskController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ContractPaymentController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'approved'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/ai', [AiController::class, 'index'])->name('ai.page');
    Route::post('/ai', [AiController::class, 'ask'])->name('ai.ask');
    Route::delete('/ai/clear', [AiController::class, 'clear'])->name('ai.clear');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Technical Support
    |--------------------------------------------------------------------------
    */
    Route::view('/technical-support', 'technical-support.index')->name('technical-support.index');

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);

        Route::get('/users/approvals', [AdminApprovalController::class, 'index'])->name('users.approvals');
        Route::post('/users/{user}/approve', [AdminApprovalController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [AdminApprovalController::class, 'reject'])->name('users.reject');
        Route::post('/users/{user}/suspend', [AdminApprovalController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/reactivate', [AdminApprovalController::class, 'reactivate'])->name('users.reactivate');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

        /*
        |--------------------------------------------------------------------------
        | Assets - Admin Only
        |--------------------------------------------------------------------------
        */
        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    });

    /*
    |--------------------------------------------------------------------------
    | HR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,hr')->group(function () {

        Route::resource('departments', DepartmentController::class);
        Route::resource('employees', EmployeeController::class);

        Route::post('/employees/{employee}/documents', [EmployeeDocumentController::class, 'store'])->name('employees.documents.store');
        Route::get('/employees/{employee}/documents/{document}/open', [EmployeeDocumentController::class, 'open'])->name('employees.documents.open');
        Route::get('/employees/{employee}/documents/{document}/download', [EmployeeDocumentController::class, 'download'])->name('employees.documents.download');
        Route::delete('/employees/{employee}/documents/{document}', [EmployeeDocumentController::class, 'destroy'])->name('employees.documents.destroy');

        Route::post('/employees/{employee}/assets', [EmployeeController::class, 'storeAsset'])->name('employees.assets.store');
        Route::delete('/employees/assets/{id}', [EmployeeController::class, 'destroyAsset'])->name('employees.assets.destroy');

        Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
        Route::post('/leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('/leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Leave Request
    |--------------------------------------------------------------------------
    */
    Route::get('/leave-request', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leave-request', [LeaveController::class, 'store'])->name('leaves.store');

    /*
    |--------------------------------------------------------------------------
    | Sales
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('sales-contracts', SalesContractController::class);

        // تسجيل دفعة للعقد
        Route::post('/sales-contracts/{contract}/payments', [ContractPaymentController::class, 'store'])
            ->name('contract-payments.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Architect
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,engineer')->group(function () {
        Route::get('/architect', [ArchitectController::class, 'index'])->name('architect.index');
        Route::post('/architect/{id}/complete', [ArchitectController::class, 'complete'])->name('architect.complete');

        Route::get('/architect-tasks', [ArchitectTaskController::class, 'index'])->name('architect-tasks.index');
        Route::get('/architect-tasks/{project}', [ArchitectTaskController::class, 'show'])->name('architect-tasks.show');
        Route::post('/architect-tasks/{project}/update', [ArchitectTaskController::class, 'updateTask'])->name('architect-tasks.update');

        Route::post('/architect-tasks/{project}/measurements', [ArchitectTaskController::class, 'storeMeasurement'])->name('architect.measurements.store');
        Route::put('/architect-measurements/{id}', [ArchitectTaskController::class, 'updateMeasurement'])->name('architect.measurements.update');
        Route::delete('/architect-measurements/{id}', [ArchitectTaskController::class, 'destroyMeasurement'])->name('architect.measurements.destroy');

        Route::post('/architect-tasks/{project}/approve', [ArchitectTaskController::class, 'approve'])->name('architect-tasks.approve');
        Route::post('/architect-tasks/{project}/send-to-factory', [ArchitectTaskController::class, 'sendToFactory'])->name('architect-tasks.sendToFactory');
    });

    /*
    |--------------------------------------------------------------------------
    | General Purchases
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/general-purchases', [PurchaseController::class, 'generalIndex'])->name('general-purchases.index');
        Route::get('/general-purchases/create', [PurchaseController::class, 'generalCreate'])->name('general-purchases.create');
        Route::post('/general-purchases', [PurchaseController::class, 'generalStore'])->name('general-purchases.store');
        Route::get('/general-purchases/{purchase}/edit', [PurchaseController::class, 'generalEdit'])->name('general-purchases.edit');
        Route::put('/general-purchases/{purchase}', [PurchaseController::class, 'generalUpdate'])->name('general-purchases.update');
        Route::delete('/general-purchases/{purchase}', [PurchaseController::class, 'generalDestroy'])->name('general-purchases.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Contract Purchases
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('purchases', PurchaseController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Warehouse
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/warehouse', function () {
            return view('warehouse.index');
        })->name('warehouse.index');

        Route::get('/warehouse/{section}', [WarehouseController::class, 'show'])
            ->name('warehouse.section.show');

        Route::get('/warehouse/{section}/input', [WarehouseController::class, 'input'])
            ->name('warehouse.section.input');

        Route::post('/warehouse/{section}', [WarehouseController::class, 'store'])
            ->name('warehouse.store');

        Route::get('/warehouse/item/{id}/edit', [WarehouseController::class, 'edit'])
            ->name('warehouse.edit');

        Route::put('/warehouse/item/{id}', [WarehouseController::class, 'update'])
            ->name('warehouse.update');

        Route::delete('/warehouse/item/{id}', [WarehouseController::class, 'destroy'])
            ->name('warehouse.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Installations
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,factory_manager,manager')->group(function () {
        Route::get('/installations', [InstallationController::class, 'index'])->name('installations.index');
        Route::get('/installations/{project}', [InstallationController::class, 'show'])->name('installations.show');
        Route::post('/installations/{id}/complete', [InstallationController::class, 'complete'])->name('installations.complete');
    });

    /*
    |--------------------------------------------------------------------------
    | Engineering Projects
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,engineer,manager')->group(function () {
        Route::resource('engineering-projects', EngineeringProjectController::class);

        Route::post('engineering-projects/{project}/updates', [ProjectUpdateController::class, 'store'])
            ->name('engineering-projects.updates.store');
        Route::delete('engineering-projects/{project}/updates/{update}', [ProjectUpdateController::class, 'destroy'])
            ->name('engineering-projects.updates.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Factory
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,factory_manager,manager')->group(function () {
        Route::get('/factory', [FactoryController::class, 'index'])->name('factory.index');

        Route::resource('production-orders', ProductionOrderController::class);
        Route::resource('production-entries', ProductionEntryController::class);
        Route::resource('production-supplies', ProductionSupplyController::class);
    });

});

require __DIR__ . '/auth.php';