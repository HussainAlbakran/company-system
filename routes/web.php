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

/* 🔥 الإضافات الجديدة */
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AiController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'approved'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | 🔥 Reports (NEW)
    |--------------------------------------------------------------------------
    */
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    /*
    |--------------------------------------------------------------------------
    |  AI System (NEW)
    |--------------------------------------------------------------------------
    */

    Route::get('/ai', [AiController::class, 'index'])->name('ai.page');
    Route::post('/ai', [AiController::class, 'ask'])->name('ai.ask');
    Route::delete('/ai/clear', [AiController::class, 'clear'])->name('ai.clear'); 

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Admin Only
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
    });

    /*
    |--------------------------------------------------------------------------
    | Admin + HR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,hr')->group(function () {

        Route::resource('departments', DepartmentController::class);
        Route::resource('employees', EmployeeController::class);

        Route::post('/employees/{employee}/documents', [EmployeeDocumentController::class, 'store'])->name('employees.documents.store');
        Route::get('/employees/{employee}/documents/{document}/open', [EmployeeDocumentController::class, 'open'])->name('employees.documents.open');
        Route::get('/employees/{employee}/documents/{document}/download', [EmployeeDocumentController::class, 'download'])->name('employees.documents.download');
        Route::delete('/employees/{employee}/documents/{document}', [EmployeeDocumentController::class, 'destroy'])->name('employees.documents.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin + Engineer + Manager
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,engineer,manager')->group(function () {

        Route::get('/engineering-projects', [EngineeringProjectController::class, 'index'])->name('engineering.projects.index');
        Route::get('/engineering-projects/create', [EngineeringProjectController::class, 'create'])->name('engineering.projects.create');
        Route::post('/engineering-projects', [EngineeringProjectController::class, 'store'])->name('engineering.projects.store');
        Route::get('/engineering-projects/{project}', [EngineeringProjectController::class, 'show'])->name('engineering.projects.show');
        Route::get('/engineering-projects/{project}/edit', [EngineeringProjectController::class, 'edit'])->name('engineering.projects.edit');
        Route::put('/engineering-projects/{project}', [EngineeringProjectController::class, 'update'])->name('engineering.projects.update');
        Route::delete('/engineering-projects/{project}', [EngineeringProjectController::class, 'destroy'])->name('engineering.projects.destroy');

        Route::post('/engineering-projects/{project}/updates', [ProjectUpdateController::class, 'store'])->name('engineering.projects.updates.store');
        Route::delete('/engineering-projects/{project}/updates/{update}', [ProjectUpdateController::class, 'destroy'])->name('engineering.projects.updates.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin + Factory Manager + Manager
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,factory_manager,manager')->group(function () {

        Route::get('/factory', [FactoryController::class, 'index'])->name('factory.index');
        Route::get('/factory/create', [FactoryController::class, 'create'])->name('factory.create');
        Route::post('/factory', [FactoryController::class, 'store'])->name('factory.store');

        Route::get('/factory/{order}', [FactoryController::class, 'show'])->name('factory.show');
        Route::get('/factory/{order}/edit', [FactoryController::class, 'edit'])->name('factory.edit');
        Route::put('/factory/{order}', [FactoryController::class, 'update'])->name('factory.update');
        Route::delete('/factory/{order}', [FactoryController::class, 'destroy'])->name('factory.destroy');

        Route::resource('products', ProductController::class);
        Route::resource('production-orders', ProductionOrderController::class);
        Route::resource('production-entries', ProductionEntryController::class);
        Route::resource('production-supplies', ProductionSupplyController::class);

        Route::post('/factory/{order}/entries', [FactoryManagerController::class, 'storeEntry'])->name('factory.entries.store');
        Route::post('/factory/{order}/supplies', [FactoryManagerController::class, 'storeSupply'])->name('factory.supplies.store');

        Route::get('/factory-manager/dashboard', [FactoryManagerController::class, 'dashboard'])->name('factory.manager.dashboard');
    });
});

require __DIR__ . '/auth.php';