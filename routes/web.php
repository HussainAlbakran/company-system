<?php

use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\AdminEmailController;
use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\ArchitectController;
use App\Http\Controllers\ArchitectMaterialRequestController;
use App\Http\Controllers\ArchitectTaskController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\EmployeeAdvanceController;
use App\Http\Controllers\FinancialCustodyController;
use App\Http\Controllers\FinancialCustodyInvoiceController;
use App\Http\Controllers\FinancialCustodySettlementController;
use App\Http\Controllers\ContractPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentParseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDocumentController;
use App\Http\Controllers\EngineeringProjectController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\FactoryInstallationRequestController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\InstallationFactoryRequestController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProductionEntryController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\ProductionSupplyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectReportController;
use App\Http\Controllers\ProjectUpdateController;
use App\Http\Controllers\PublicHomeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesContractController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicHomeController::class, 'index'])->name('home');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware(['auth', 'approved', 'basic_user_restricted', 'finance.restricted'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/dismiss', [DashboardController::class, 'dismiss'])->name('dashboard.dismiss');

    Route::get('/custody-invoices', [FinancialCustodyInvoiceController::class, 'index'])->name('custody-invoices.index');
    Route::post('/custody-invoices', [FinancialCustodyInvoiceController::class, 'store'])->name('custody-invoices.store');
    Route::put('/custody-invoices/{invoice}', [FinancialCustodyInvoiceController::class, 'update'])->name('custody-invoices.update');
    Route::get('/custody-invoices/{invoice}/attachment', [FinancialCustodyInvoiceController::class, 'attachment'])->name('custody-invoices.attachment');

    Route::get('/designs', function () {
        if (! auth()->user()->canAccessDesignsModule()) {
            abort(403, __('common.forbidden'));
        }

        return view('designs.index');
    })->name('designs.index');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('/ai', [AiController::class, 'index'])->name('ai.page');
        Route::post('/ai', [AiController::class, 'ask'])->name('ai.ask');
        Route::delete('/ai/clear', [AiController::class, 'clear'])->name('ai.clear');

        /*
        |--------------------------------------------------------------------------
        | Technical Support
        |--------------------------------------------------------------------------
        */
        Route::view('/technical-support', 'technical-support.index')->name('technical-support.index');
        Route::view('/support', 'technical-support.index')->name('support.index');

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::prefix('administration')->name('administration.')->group(function () {
                Route::get('/', [AdministrationController::class, 'index'])->name('index');
                Route::get('/assignments', [AdministrationController::class, 'assignments'])->name('assignments');
                Route::get('/updates', [AdministrationController::class, 'updates'])->name('updates');
            });

            Route::resource('users', UserManagementController::class)->except(['show']);

            Route::get('/users/approvals', [AdminApprovalController::class, 'index'])->name('users.approvals');
            Route::post('/users/{user}/approve', [AdminApprovalController::class, 'approve'])->name('users.approve');
            Route::post('/users/{user}/reject', [AdminApprovalController::class, 'reject'])->name('users.reject');
            Route::post('/users/{user}/suspend', [AdminApprovalController::class, 'suspend'])->name('users.suspend');
            Route::post('/users/{user}/reactivate', [AdminApprovalController::class, 'reactivate'])->name('users.reactivate');

            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

            Route::get('/admin-emails', [AdminEmailController::class, 'index'])->name('admin-emails.index');
            Route::post('/admin-emails/send', [AdminEmailController::class, 'send'])
                ->middleware('throttle:5,1')
                ->name('admin-emails.send');

        });

        Route::middleware('admin.finance')->group(function () {
            Route::get('/cash-flow', [CashFlowController::class, 'index'])->name('cash-flow.index');
            Route::get('/cash-flow/maintenance', [CashFlowController::class, 'maintenance'])->name('cash-flow.maintenance');
            Route::post('/cash-flow', [CashFlowController::class, 'store'])->name('cash-flow.store');
            Route::delete('/cash-flow/{entry}', [CashFlowController::class, 'destroy'])->name('cash-flow.destroy');

            Route::get('/financial-custodies', [FinancialCustodyController::class, 'index'])->name('financial-custodies.index');
            Route::get('/financial-custodies/create', [FinancialCustodyController::class, 'create'])->name('financial-custodies.create');
            Route::post('/financial-custodies', [FinancialCustodyController::class, 'store'])->name('financial-custodies.store');
            Route::get('/financial-custodies/{financialCustody}', [FinancialCustodyController::class, 'show'])->name('financial-custodies.show');
            Route::post('/financial-custodies/{financialCustody}/settle-full', [FinancialCustodyController::class, 'settleFull'])->name('financial-custodies.settle-full');
            Route::post('/financial-custodies/{financialCustody}/settle-partial', [FinancialCustodyController::class, 'settlePartial'])->name('financial-custodies.settle-partial');
            Route::post('/financial-custodies/{financialCustody}/return-remaining', [FinancialCustodyController::class, 'returnRemaining'])->name('financial-custodies.return-remaining');

            Route::get('/custody-settlements', [FinancialCustodySettlementController::class, 'index'])->name('custody-settlements.index');
            Route::get('/custody-settlements/records', [FinancialCustodySettlementController::class, 'records'])->name('custody-settlements.records');
            Route::get('/custody-settlements/open/{financialCustody}', [FinancialCustodySettlementController::class, 'open'])->name('custody-settlements.open');
            Route::get('/custody-settlements/{settlement}', [FinancialCustodySettlementController::class, 'show'])->name('custody-settlements.show');
            Route::put('/custody-settlements/{settlement}', [FinancialCustodySettlementController::class, 'update'])->name('custody-settlements.update');
            Route::post('/custody-settlements/{settlement}/approve', [FinancialCustodySettlementController::class, 'approve'])->name('custody-settlements.approve');
            Route::post('/custody-settlements/{settlement}/lines/{invoice}/attachment', [FinancialCustodySettlementController::class, 'uploadAttachment'])->name('custody-settlements.upload-attachment');
            Route::get('/custody-settlement-invoices/{invoice}/attachment', [FinancialCustodySettlementController::class, 'attachment'])->name('custody-settlements.attachment');

            Route::get('/employee-advances', [EmployeeAdvanceController::class, 'index'])->name('employee-advances.index');
            Route::get('/employee-advances/create', [EmployeeAdvanceController::class, 'create'])->name('employee-advances.create');
            Route::post('/employee-advances', [EmployeeAdvanceController::class, 'store'])->name('employee-advances.store');
        });

        /*
        |--------------------------------------------------------------------------
        | HR
        |--------------------------------------------------------------------------
        */
        Route::middleware('hr.module')->group(function () {

            Route::resource('departments', DepartmentController::class);
            Route::get('/employees/payroll-registers', [EmployeeController::class, 'payrollRegistersIndex'])
                ->name('employees.payroll-registers.index');
            Route::post('/employees/payroll-registers/new', [EmployeeController::class, 'createPayrollRegister'])
                ->name('employees.payroll-registers.create');
            Route::post('/employees/payroll-register/{payrollRegister}/adjustments', [EmployeeController::class, 'updatePayrollRegisterAdjustments'])
                ->name('employees.payroll-register.update-adjustments');
            Route::get('/employees/payroll-register/{payrollRegister}', [EmployeeController::class, 'showPayrollRegister'])
                ->name('employees.payroll-register.show');
            Route::get('/employees/payroll-register', [EmployeeController::class, 'payrollRegister'])
                ->name('employees.payroll-register');
            Route::get('/employees/salary-slip', [EmployeeController::class, 'salarySlip'])
                ->name('employees.salary-slip');
            Route::get('/employees/leave-register', [EmployeeController::class, 'leaveRegister'])
                ->name('employees.leave-register');
            Route::get('/employees/residency-expiring', [EmployeeController::class, 'residencyExpiring'])
                ->name('employees.residency-expiring');
            Route::get('/employees/contracts-expiring', [EmployeeController::class, 'contractsExpiring'])
                ->name('employees.contracts-expiring');
            Route::post('/employees/payroll-register/approve', [EmployeeController::class, 'approvePayrollRegister'])->name('employees.payroll-register.approve');
            Route::resource('employees', EmployeeController::class);

            Route::post('/employees/{employee}/documents', [EmployeeDocumentController::class, 'store'])->name('employees.documents.store');
            Route::post('/employees/{employee}/payroll-adjustment', [EmployeeController::class, 'savePayrollAdjustment'])->name('employees.payroll-adjustment.save');
            Route::get('/employees/{employee}/documents/{document}/open', [EmployeeDocumentController::class, 'open'])->name('employees.documents.open');
            Route::get('/employees/{employee}/documents/{document}/download', [EmployeeDocumentController::class, 'download'])->name('employees.documents.download');
            Route::delete('/employees/{employee}/documents/{document}', [EmployeeDocumentController::class, 'destroy'])->name('employees.documents.destroy');

            Route::post('/employees/{employee}/assets', [EmployeeController::class, 'storeAsset'])->name('employees.assets.store');
            Route::delete('/employees/assets/{id}', [EmployeeController::class, 'destroyAsset'])->name('employees.assets.destroy');

            Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
            Route::post('/leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
            Route::post('/leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

            /*
            |--------------------------------------------------------------------------
            | Assets - HR + Admin
            |--------------------------------------------------------------------------
            */
            Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
            Route::get('/assets/with-employees', [AssetController::class, 'assignedWithEmployees'])->name('assets.with-employees');
            Route::get('/assets/registration-expiring-soon', [AssetController::class, 'registrationExpiringSoon'])->name('assets.registration-expiring-soon');
            Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
            Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
            Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
            Route::post('/assets/{asset}/assign', [AssetController::class, 'assignToEmployee'])->name('assets.assign');
            Route::post('/assets/{asset}/transfer-maintenance', [AssetController::class, 'transferToMaintenance'])->name('assets.transfer-maintenance');
            Route::post('/assets/{asset}/end-maintenance', [AssetController::class, 'endMaintenance'])->name('assets.end-maintenance');
            Route::post('/assets/assignments/{assignment}/return', [AssetController::class, 'returnAssignment'])->name('assets.assignments.return');
        });

        /*
        |--------------------------------------------------------------------------
        | Leave Request — available to every approved authenticated user
        | (approve/reject remain under hr.module above)
        |--------------------------------------------------------------------------
        */
        Route::get('/leave-request', [LeaveController::class, 'create'])->name('leaves.create');
        Route::post('/leave-request', [LeaveController::class, 'store'])->name('leaves.store');

        Route::middleware('role:super_admin,admin,hr_manager,hr,engineering_manager,engineer,operations_manager,factory_manager,manager,procurement_manager,procurement')->group(function () {
            Route::post('/parse-document', [DocumentParseController::class, 'parse'])->name('documents.parse');
        });

        /*
        |--------------------------------------------------------------------------
        | Sales
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:super_admin,admin,sales_manager,sales,manager')->group(function () {
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
        Route::middleware('role:super_admin,admin,engineering_manager,engineer,operations_manager')->group(function () {
            Route::get('/architect', [ArchitectController::class, 'index'])->name('architect.index');

            Route::get('/architect-tasks', [ArchitectTaskController::class, 'index'])->name('architect-tasks.index');
            Route::get('/architect-tasks/{project}', [ArchitectTaskController::class, 'show'])->name('architect-tasks.show');
            Route::post('/architect-tasks/{project}/update', [ArchitectTaskController::class, 'updateTask'])->name('architect-tasks.update');

            Route::post('/architect-tasks/{project}/measurements', [ArchitectTaskController::class, 'storeMeasurement'])->name('architect.measurements.store');
            Route::put('/architect-measurements/{id}', [ArchitectTaskController::class, 'updateMeasurement'])->name('architect.measurements.update');
            Route::delete('/architect-measurements/{id}', [ArchitectTaskController::class, 'destroyMeasurement'])->name('architect.measurements.destroy');

            Route::post('/architect-tasks/{project}/send-to-factory', [ArchitectTaskController::class, 'sendToFactory'])->name('architect-tasks.sendToFactory');

            Route::get('/architect/projects/{project}/material-requirements', [ArchitectMaterialRequestController::class, 'materialRequirements'])
                ->name('architect.project-material-requirements');

            Route::get('/architect-tasks/{project}/material-requests/create', [ArchitectMaterialRequestController::class, 'create'])
                ->name('architect.material-requests.create');
            Route::post('/architect-tasks/{project}/material-requests', [ArchitectMaterialRequestController::class, 'store'])
                ->name('architect.material-requests.store');
            Route::get('/architect-tasks/{project}/material-requests/{materialRequest}/edit', [ArchitectMaterialRequestController::class, 'edit'])
                ->name('architect.material-requests.edit');
            Route::put('/architect-tasks/{project}/material-requests/{materialRequest}', [ArchitectMaterialRequestController::class, 'update'])
                ->name('architect.material-requests.update');
        });

        /*
        |--------------------------------------------------------------------------
        | General Purchases
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:super_admin,admin,finance,procurement_manager,procurement,manager')->group(function () {
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
        Route::middleware('role:super_admin,admin,finance,procurement_manager,procurement,manager')->group(function () {
            Route::get('/purchases/material-requests', [ArchitectMaterialRequestController::class, 'purchasesIndex'])
                ->name('purchases.material-requests.index');
            Route::get('/purchases/material-requests/{materialRequest}', [ArchitectMaterialRequestController::class, 'purchasesShow'])
                ->name('purchases.material-requests.show');
            Route::post('/purchases/material-requests/{materialRequest}/status', [ArchitectMaterialRequestController::class, 'updateStatus'])
                ->name('purchases.material-requests.status');
            Route::post('/purchases/material-requests/{materialRequest}/approve', [ArchitectMaterialRequestController::class, 'approve'])
                ->name('purchases.material-requests.approve');
            Route::post('/purchases/material-requests/{materialRequest}/reject', [ArchitectMaterialRequestController::class, 'reject'])
                ->name('purchases.material-requests.reject');
            Route::post('/purchases/material-requests/{materialRequest}/convert', [ArchitectMaterialRequestController::class, 'convertToPurchases'])
                ->name('purchases.material-requests.convert');

            Route::resource('purchases', PurchaseController::class);
        });

        Route::get('/architect-material-requests/{materialRequest}/attachment', [ArchitectMaterialRequestController::class, 'downloadAttachment'])
            ->name('architect.material-requests.attachment');

        /*
        |--------------------------------------------------------------------------
        | Warehouse
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:super_admin,admin,procurement_manager,procurement,manager')->group(function () {
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
        Route::middleware('role:super_admin,admin,operations_manager,factory_manager,manager')->group(function () {
            Route::get('/installations', [InstallationController::class, 'index'])->name('installations.index');
            Route::get('/installations/{project}', [InstallationController::class, 'show'])->name('installations.show');
            Route::post('/installations/{id}/complete', [InstallationController::class, 'complete'])->name('installations.complete');

            Route::get('/installations/{project}/factory-requests/create', [InstallationFactoryRequestController::class, 'create'])
                ->name('installations.factory-requests.create');
            Route::post('/installations/{project}/factory-requests', [InstallationFactoryRequestController::class, 'store'])
                ->name('installations.factory-requests.store');
            Route::get('/installations/{project}/factory-requests/{installationFactoryRequest}/edit', [InstallationFactoryRequestController::class, 'edit'])
                ->name('installations.factory-requests.edit');
            Route::put('/installations/{project}/factory-requests/{installationFactoryRequest}', [InstallationFactoryRequestController::class, 'update'])
                ->name('installations.factory-requests.update');
        });

        /*
        |--------------------------------------------------------------------------
        | Engineering Projects
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:super_admin,admin,engineering_manager,engineer,operations_manager,manager')->group(function () {
            Route::resource('engineering-projects', EngineeringProjectController::class);

            Route::post('engineering-projects/{project}/updates', [ProjectUpdateController::class, 'store'])
                ->name('engineering-projects.updates.store');
            Route::delete('engineering-projects/{project}/updates/{update}', [ProjectUpdateController::class, 'destroy'])
                ->name('engineering-projects.updates.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Project Reports
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::get('/project-reports/board', [ProjectReportController::class, 'board'])
                ->name('project-reports.board');
            Route::get('/project-reports/archive', [ProjectReportController::class, 'archive'])
                ->name('project-reports.archive');
            Route::get('/project-reports/project/{project}', [ProjectReportController::class, 'show'])
                ->name('project-reports.show');
            Route::get('/project-reports/project/{project}/material-requests/{materialRequest}/attachment', [ProjectReportController::class, 'downloadMaterialRequestAttachment'])
                ->name('project-reports.material-attachment');
            Route::post('/project-reports/project/{project}/complete', [ProjectReportController::class, 'complete'])
                ->name('project-reports.complete');
            Route::delete('/project-reports/{projectReport}', [ProjectReportController::class, 'destroy'])
                ->name('project-reports.destroy');
        });

        Route::middleware('role:super_admin,admin,operations_manager,engineering_manager,engineer')->group(function () {
            Route::get('/project-reports/create', [ProjectReportController::class, 'create'])
                ->name('project-reports.create');
            Route::post('/project-reports', [ProjectReportController::class, 'store'])
                ->name('project-reports.store');
            Route::get('/project-reports/{projectReport}/download', [ProjectReportController::class, 'download'])
                ->name('project-reports.download');
        });

        /*
        |--------------------------------------------------------------------------
        | Factory
        |--------------------------------------------------------------------------
        */
    Route::middleware('role:super_admin,admin,operations_manager,factory_manager,manager')->group(function () {
            Route::get('/factory', [FactoryController::class, 'index'])->name('factory.index');

            Route::get('/factory/installation-requests', [FactoryInstallationRequestController::class, 'index'])
                ->name('factory.installation-requests.index');
            Route::get('/factory/installation-requests/{installationFactoryRequest}', [FactoryInstallationRequestController::class, 'show'])
                ->name('factory.installation-requests.show');
            Route::patch('/factory/installation-requests/{installationFactoryRequest}/status', [FactoryInstallationRequestController::class, 'updateStatus'])
                ->name('factory.installation-requests.update-status');

            Route::resource('production-orders', ProductionOrderController::class);
            Route::resource('production-entries', ProductionEntryController::class);
            Route::resource('production-supplies', ProductionSupplyController::class);
        });

});

require __DIR__.'/auth.php';
