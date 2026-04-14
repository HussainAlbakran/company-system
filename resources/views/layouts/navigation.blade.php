<aside class="sidebar">
    <div class="brand-box">
        <h2 class="brand-title">Construction ERP</h2>
        <p class="brand-subtitle">Enterprise Operations</p>
    </div>

    <div class="nav-links">
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('reports.index') }}"
           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>Reports</span>
        </a>

        <a href="{{ route('sales-contracts.index') }}"
           class="nav-link {{ request()->routeIs('sales-contracts.*') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>Contracts</span>
        </a>

        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'engineer')
            <a href="{{ route('architect.index') }}"
               class="nav-link {{ request()->routeIs('architect.index') || request()->routeIs('architect.complete') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Architect</span>
            </a>

            <a href="{{ route('architect-tasks.index') }}"
               class="nav-link {{ request()->routeIs('architect-tasks.*') || request()->routeIs('architect.measurements.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Design</span>
            </a>
        @endif

        @if(auth()->user()->canManageProduction())
            <a href="{{ route('factory.index') }}"
               class="nav-link {{ request()->routeIs('factory.*') || request()->routeIs('production-orders.*') || request()->routeIs('production-entries.*') || request()->routeIs('production-supplies.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Factory</span>
            </a>
        @endif

        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'factory_manager' || auth()->user()->role == 'manager')
            <a href="{{ route('installations.index') }}"
               class="nav-link {{ request()->routeIs('installations.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Installation</span>
            </a>
        @endif

        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
            <a href="{{ route('general-purchases.index') }}"
               class="nav-link {{ request()->routeIs('general-purchases.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>General Purchases</span>
            </a>

            <a href="{{ route('purchases.index') }}"
               class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Contract Purchases</span>
            </a>

            <a href="{{ route('warehouse.index') }}"
               class="nav-link {{ request()->routeIs('warehouse.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Warehouse</span>
            </a>
        @endif

        @if(auth()->user()->role == 'admin')
            <a href="{{ route('assets.index') }}"
               class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Assets</span>
            </a>
        @endif

        @if(auth()->user()->canManageEmployees())
            <a href="{{ route('employees.index') }}"
               class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Employees</span>
            </a>
        @endif

        @if(auth()->user()->canManageDepartments())
            <a href="{{ route('departments.index') }}"
               class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Departments</span>
            </a>
        @endif

        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'hr')
            <a href="{{ route('leaves.index') }}"
               class="nav-link {{ request()->routeIs('leaves.index') || request()->routeIs('leaves.approve') || request()->routeIs('leaves.reject') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Leave Management</span>
            </a>
        @endif

        <a href="{{ route('leaves.create') }}"
           class="nav-link {{ request()->routeIs('leaves.create') || request()->routeIs('leaves.store') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>Request Leave</span>
        </a>

        @if(auth()->user()->canManageUsers())
            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->routeIs('users.*') && !request()->routeIs('users.approvals') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Users</span>
            </a>

            <a href="{{ route('users.approvals') }}"
               class="nav-link {{ request()->routeIs('users.approvals') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>User Approvals</span>
            </a>
        @endif

        @if(auth()->user()->canViewAuditLogs())
            <a href="{{ route('audit.index') }}"
               class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>Audit Logs</span>
            </a>
        @endif

        <a href="{{ route('ai.page') }}"
           class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>AI Assistant</span>
        </a>

        <a href="{{ route('technical-support.index') }}"
           class="nav-link {{ request()->routeIs('technical-support.*') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>Technical Support</span>
        </a>

        <a href="{{ route('profile.edit') }}"
           class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>Profile</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" style="margin-top: 8px;">
            @csrf
            <button type="submit" class="btn btn-danger" style="width:100%;">
                Logout
            </button>
        </form>
    </div>
</aside>
