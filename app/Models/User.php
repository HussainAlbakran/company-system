<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'approval_status',
        'is_active',
        'approved_at',
        'approved_by',
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
        return $this->role === 'admin';
    }

    public function isHR(): bool
    {
        return $this->role === 'hr';
    }

    public function isEngineer(): bool
    {
        return $this->role === 'engineer';
    }

    public function isFactoryManager(): bool
    {
        return $this->role === 'factory_manager';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    // =========================
    // Permissions
    // =========================

    public function canManageUsers(): bool
    {
        return $this->role === 'admin';
    }

    public function canManageEmployees(): bool
    {
        return in_array($this->role, ['admin', 'hr']);
    }

    public function canManageDepartments(): bool
    {
        return in_array($this->role, ['admin', 'hr']);
    }

    public function canManageProjects(): bool
    {
        return in_array($this->role, ['admin', 'engineer', 'manager']);
    }

    public function canManageProduction(): bool
    {
        return in_array($this->role, ['admin', 'factory_manager', 'manager']);
    }

    public function canViewAuditLogs(): bool
    {
        return $this->role === 'admin';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}