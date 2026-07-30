<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_FINANCE = 'finance';

    /**
     * All roles assignable from user management.
     */
    public const MANAGEABLE_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_FINANCE,
        'sales_manager',
        'sales',
        'engineering_manager',
        'engineer',
        'procurement_manager',
        'procurement',
        'hr_manager',
        'hr',
        'operations_manager',
        'factory_manager',
        'manager',
        'user',
    ];

    /**
     * Roles assignable when creating a system account from the employee screen.
     * Kept identical to user-management roles (single source of truth).
     */
    public const EMPLOYEE_ACCOUNT_ROLES = self::MANAGEABLE_ROLES;

    /** مدير النظام، الإدارة، وموارد البشرية */
    public const HR_MODULE_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        'hr_manager',
        'hr',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'approval_status',
        'is_active',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            if ($user->portalLinkedProjects()->exists()) {
                throw ValidationException::withMessages([
                    'user' => [__('users.cannot_delete_portal_linked')],
                ]);
            }
        });
    }

    // =========================
    // Relationships
    // =========================

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // =========================
    // Roles
    // =========================

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdminLike(): bool
    {
        return $this->hasAnyRole([self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function isHR(): bool
    {
        return $this->role === 'hr' || $this->role === 'hr_manager';
    }

    public function isEngineer(): bool
    {
        return $this->role === 'engineer' || $this->role === 'engineering_manager';
    }

    public function isFactoryManager(): bool
    {
        return $this->role === 'factory_manager';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isBasicUser(): bool
    {
        return $this->role === 'user';
    }

    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }

    public function canViewProjectFinancials(): bool
    {
        return $this->isAdminLike();
    }

    public function canViewProjectValueOnly(): bool
    {
        return $this->role === 'operations_manager';
    }

    public function canAccessDesignsModule(): bool
    {
        return in_array($this->role, [
            'super_admin',
            'admin',
            'engineering_manager',
            'engineer',
            'operations_manager',
        ], true);
    }

    public function getRoleLabel(): string
    {
        $key = 'roles.'.$this->role;
        $label = __($key);

        return $label !== $key ? $label : $this->role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isApprovedAndActive(): bool
    {
        return $this->approval_status === 'approved' && $this->is_active;
    }

    /**
     * «طلب إجازة» navigation: every approved active user may open the request page.
     */
    public function canAccessLeaveRequestNavigation(): bool
    {
        return $this->canCreateLeaveRequest();
    }

    /**
     * AI assistant: at least one data module is allowed for this role (see aiAllowedModules()).
     */
    public function canAccessAiAssistantNavigation(): bool
    {
        return $this->isApprovedAndActive() && count($this->aiAllowedModules()) > 0;
    }

    /**
     * Engineering / ERP projects linked to external portal user.
     */
    public function portalLinkedProjects()
    {
        return $this->hasMany(Project::class, 'client_user_id');
    }

    // =========================
    // Permissions
    // =========================

    public function canManageUsers(): bool
    {
        return $this->isAdminLike();
    }

    public function canAccessContractsModule(): bool
    {
        return $this->isAdminLike()
            || $this->hasAnyRole(['sales_manager', 'sales'])
            || $this->hasAnyRole(['manager']); // legacy compatibility
    }

    /** مشتريات العقود وطلبات المواد المرتبطة (بدون المستودع) */
    public function canAccessContractPurchasesModule(): bool
    {
        return $this->isAdminLike()
            || $this->isFinance()
            || $this->hasAnyRole(['procurement_manager', 'procurement'])
            || $this->hasAnyRole(['manager']); // legacy compatibility
    }

    /** المشتريات العامة فقط (بدون مشتريات العقود أو المستودع) */
    public function canAccessGeneralPurchasesModule(): bool
    {
        return $this->isAdminLike()
            || $this->isFinance()
            || $this->hasAnyRole(['procurement_manager', 'procurement'])
            || $this->hasAnyRole(['manager']); // legacy compatibility
    }

    public function canAccessEngineeringModule(): bool
    {
        return $this->isAdminLike()
            || $this->hasAnyRole(['engineering_manager', 'engineer', 'operations_manager'])
            || $this->hasAnyRole(['engineer']); // legacy compatibility
    }

    /**
     * تقارير المشاريع + المشاريع السابقة: الإدارة العليا ومدير النظام فقط.
     */
    public function canViewProjectReportsBoard(): bool
    {
        return $this->hasAnyRole([self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    /**
     * تسجيل تقارير المشاريع: الإدارة العليا، مدير النظام، مدير الهندسة، المهندس، مدير العمليات.
     */
    public function canSubmitProjectReports(): bool
    {
        return $this->hasAnyRole([
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            'operations_manager',
            'engineering_manager',
            'engineer',
        ]);
    }

    public function canAccessProjectReportsModule(): bool
    {
        return $this->canViewProjectReportsBoard() || $this->canSubmitProjectReports();
    }

    public function canAccessProcurementModule(): bool
    {
        if ($this->isFinance()) {
            return false;
        }

        return $this->isAdminLike()
            || $this->hasAnyRole(['procurement_manager', 'procurement'])
            || $this->hasAnyRole(['manager']); // legacy compatibility
    }

    public function canAccessHRModule(): bool
    {
        return $this->hasAnyRole(self::HR_MODULE_ROLES);
    }

    public function canAccessOperationsModule(): bool
    {
        return $this->isAdminLike()
            || $this->role === 'operations_manager'
            || $this->hasAnyRole(['factory_manager', 'manager']); // legacy compatibility
    }

    public function canManageEmployees(): bool
    {
        return $this->canAccessHRModule();
    }

    public function canManageDepartments(): bool
    {
        return $this->canAccessHRModule();
    }

    public function canManageAssets(): bool
    {
        return $this->canAccessHRModule();
    }

    public function canManageLeaveApprovals(): bool
    {
        return $this->canAccessHRModule();
    }

    public function canCreateLeaveRequest(): bool
    {
        return $this->isApprovedAndActive();
    }

    public function canManageProjects(): bool
    {
        return $this->canAccessEngineeringModule();
    }

    public function canManageProduction(): bool
    {
        return $this->canAccessOperationsModule();
    }

    public function canManageInstallations(): bool
    {
        return $this->canAccessOperationsModule();
    }

    public function canViewAuditLogs(): bool
    {
        return $this->isAdminLike();
    }

    /** المدخول والصرف — مدير النظام والإدارة والمالية */
    public function canAccessCashFlowModule(): bool
    {
        return $this->isAdminLike() || $this->isFinance();
    }

    public function aiAllowedModules(): array
    {
        if ($this->isAdminLike()) {
            return ['employees', 'departments', 'assets', 'leaves', 'contracts', 'engineering', 'projects', 'factory', 'installation', 'purchases', 'warehouse', 'cash_flow'];
        }

        if ($this->isFinance()) {
            return ['purchases', 'cash_flow'];
        }

        $modules = [];
        if ($this->canAccessHRModule()) {
            $modules = array_merge($modules, ['employees', 'departments', 'assets', 'leaves']);
        }
        if ($this->canAccessContractsModule()) {
            $modules[] = 'contracts';
        }
        if ($this->canAccessEngineeringModule()) {
            $modules = array_merge($modules, ['engineering', 'projects']);
        }
        if ($this->canAccessOperationsModule()) {
            $modules = array_merge($modules, ['factory', 'installation']);
        }
        if ($this->canAccessProcurementModule()) {
            $modules = array_merge($modules, ['purchases', 'warehouse']);
        }

        return array_values(array_unique($modules));
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        if (! $this->isApprovedAndActive()) {
            return false;
        }

        return $this->isAdminLike()
            || $this->hasAnyRole([
                'sales_manager',
                'sales',
                'engineering_manager',
                'engineer',
                'procurement_manager',
                'procurement',
                'hr_manager',
                'hr',
                'operations_manager',
                'factory_manager',
                'manager',
            ]);
    }
}
