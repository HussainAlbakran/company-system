<aside class="sidebar">

    <div class="brand-box">
        <h2 class="brand-title">شركة التقدم للخرسانة الجاهزة</h2>
        <p class="brand-subtitle" </p>
    </div>

    <div class="nav-links">

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        @if(auth()->user()->canManageProjects())
            <a href="{{ route('engineering.projects.index') }}"
               class="nav-link {{ request()->routeIs('engineering.projects.*') ? 'active' : '' }}">
                Projects
            </a>
        @endif

        @if(auth()->user()->canManageProduction())
            <a href="{{ route('factory.index') }}"
               class="nav-link {{ request()->routeIs('factory.*') ? 'active' : '' }}">
                Factory
            </a>
        @endif

        @if(auth()->user()->canManageEmployees())
            <a href="{{ route('employees.index') }}"
               class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                Employees
            </a>
        @endif

        @if(auth()->user()->canManageDepartments())
            <a href="{{ route('departments.index') }}"
               class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                Departments
            </a>
        @endif

        @if(auth()->user()->canManageUsers())
            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                Users
            </a>

            <a href="{{ route('users.approvals') }}"
               class="nav-link {{ request()->routeIs('users.approvals') ? 'active' : '' }}">
                User Approvals
            </a>
        @endif

        @if(auth()->user()->canViewAuditLogs())
            <a href="{{ route('audit.index') }}"
               class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                Audit Logs
            </a>
        @endif

        {{-- 🔥 التقارير --}}
        <a href="{{ route('reports.index') }}"
           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            Reports
        </a>

        {{--  الذكاء --}}
        <a href="{{ route('ai.page') }}"
           class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}">
             AI 
        </a>

        <a href="{{ route('profile.edit') }}"
           class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            Profile
        </a>

        <form method="POST" action="{{ route('logout') }}" style="margin-top: 12px;">
            @csrf
            <button type="submit" class="btn btn-danger" style="width:100%;">
                Logout
            </button>
        </form>

    </div>

</aside>