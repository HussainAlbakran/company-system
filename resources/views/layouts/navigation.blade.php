<aside class="sidebar">
    <div class="brand-box">
        <h2 class="brand-title">{{ __('navigation.brand_title') }}</h2>
        <p class="brand-subtitle">{{ __('navigation.brand_subtitle') }}</p>
    </div>

    <div class="nav-links">
        @php($navUser = auth()->user())

        {{-- الإدارة العليا: قائمة مخصّصة فقط --}}
        @if($navUser->isSuperAdmin())
            <div class="nav-section-label">{{ __('navigation.control_center') }}</div>
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.dashboard') }}</span>
            </a>

            <a href="{{ route('project-reports.board') }}"
               class="nav-link {{ request()->routeIs('project-reports.board') || request()->routeIs('project-reports.show') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.project_reports') }}</span>
            </a>

            <a href="{{ route('project-reports.archive') }}"
               class="nav-link {{ request()->routeIs('project-reports.archive') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.project_reports_archive') }}</span>
            </a>

            <a href="{{ route('project-reports.create') }}"
               class="nav-link {{ request()->routeIs('project-reports.create') || request()->routeIs('project-reports.store') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.project_reports_register') }}</span>
            </a>

            <a href="{{ route('leaves.index') }}"
               class="nav-link {{ request()->routeIs('leaves.index') || request()->routeIs('leaves.approve') || request()->routeIs('leaves.reject') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.leave_management') }}</span>
            </a>

            <a href="{{ route('administration.index') }}"
               class="nav-link {{ request()->routeIs('administration.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.administration') }}</span>
            </a>

            <a href="{{ route('profile.show') }}"
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.profile') }}</span>
            </a>

        @elseif(! $navUser->isBasicUser())
        <div class="nav-section-label">{{ __('navigation.control_center') }}</div>
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>{{ __('navigation.dashboard') }}</span>
        </a>

        @if($navUser->canAccessContractsModule())
            <a href="{{ route('sales-contracts.index') }}"
               class="nav-link {{ request()->routeIs('sales-contracts.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.contracts') }}</span>
            </a>
        @endif

        @if($navUser->canAccessEngineeringModule())
            <a href="{{ route('architect.index') }}"
               class="nav-link {{ request()->routeIs('architect.index') || request()->routeIs('architect.project-material-requirements') || request()->routeIs('architect.material-requests.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.architect') }}</span>
            </a>

            <a href="{{ route('architect-tasks.index') }}"
               class="nav-link {{ request()->routeIs('architect-tasks.*') || request()->routeIs('architect.measurements.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.designs') }}</span>
            </a>
        @endif

        @if($navUser->canViewProjectReportsBoard())
            <a href="{{ route('project-reports.board') }}"
               class="nav-link {{ request()->routeIs('project-reports.board') || request()->routeIs('project-reports.show') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.project_reports') }}</span>
            </a>

            <a href="{{ route('project-reports.archive') }}"
               class="nav-link {{ request()->routeIs('project-reports.archive') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.project_reports_archive') }}</span>
            </a>
        @endif

        @if($navUser->canSubmitProjectReports())
            <a href="{{ route('project-reports.create') }}"
               class="nav-link {{ request()->routeIs('project-reports.create') || request()->routeIs('project-reports.store') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.project_reports_register') }}</span>
            </a>
        @endif

        @if($navUser->canManageProduction())
            <a href="{{ route('factory.index') }}"
               class="nav-link {{ request()->routeIs('factory.*') || request()->routeIs('factory.installation-requests.*') || request()->routeIs('production-orders.*') || request()->routeIs('production-entries.*') || request()->routeIs('production-supplies.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.factory') }}</span>
                @if(($pendingInstallationFactoryRequestsCount ?? 0) > 0)
                    <span class="badge badge-orange" style="margin-inline-start:6px;" title="{{ __('navigation.factory_pending_installations') }}">{{ $pendingInstallationFactoryRequestsCount }}</span>
                @endif
            </a>
        @endif

        @if($navUser->canManageInstallations())
            <a href="{{ route('installations.index') }}"
               class="nav-link {{ request()->routeIs('installations.*') || request()->routeIs('installations.factory-requests.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.installations') }}</span>
            </a>
        @endif

        @if($navUser->canAccessGeneralPurchasesModule())
            <a href="{{ route('general-purchases.index') }}"
               class="nav-link {{ request()->routeIs('general-purchases.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.general_purchases') }}</span>
            </a>
        @endif

        @if($navUser->canAccessContractPurchasesModule())
            <a href="{{ route('purchases.index') }}"
               class="nav-link {{ request()->routeIs('purchases.*') && !request()->routeIs('general-purchases.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.contract_purchases') }}</span>
            </a>
        @endif

        @if($navUser->canAccessProcurementModule())
            <a href="{{ route('warehouse.index') }}"
               class="nav-link {{ request()->routeIs('warehouse.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.warehouse') }}</span>
            </a>
        @endif

        @if($navUser->canManageAssets())
            <a href="{{ route('assets.index') }}"
               class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.assets') }}</span>
            </a>
        @endif

        @if($navUser->canManageEmployees())
            <a href="{{ route('employees.index') }}"
               class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.employees') }}</span>
            </a>
        @endif

        @if($navUser->canManageDepartments())
            <a href="{{ route('departments.index') }}"
               class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.departments') }}</span>
            </a>
        @endif

        @if($navUser->canManageLeaveApprovals())
            <a href="{{ route('leaves.index') }}"
               class="nav-link {{ request()->routeIs('leaves.index') || request()->routeIs('leaves.approve') || request()->routeIs('leaves.reject') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.leave_management') }}</span>
            </a>
        @endif

        @if($navUser->canAccessLeaveRequestNavigation())
        <a href="{{ route('leaves.create') }}"
           class="nav-link {{ request()->routeIs('leaves.create') || request()->routeIs('leaves.store') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>{{ __('navigation.leave_request') }}</span>
        </a>
        @endif

        @if($navUser->isAdminLike())
            <a href="{{ route('administration.index') }}"
               class="nav-link {{ request()->routeIs('administration.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.administration') }}</span>
            </a>
        @endif

        @if($navUser->canAccessCashFlowModule())
            <a href="{{ route('cash-flow.index') }}"
               class="nav-link {{ request()->routeIs('cash-flow.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.cash_flow') }}</span>
            </a>
            <a href="{{ route('custody-settlements.index') }}"
               class="nav-link {{ request()->routeIs('custody-settlements.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.custody_settlement') }}</span>
            </a>
        @endif

        @php
            $hasOpenCustody = $navUser?->employee
                && \App\Models\FinancialCustody::hasOpenForEmployee($navUser->employee->id);
        @endphp
        @if($hasOpenCustody)
            <a href="{{ route('custody-invoices.index') }}"
               class="nav-link {{ request()->routeIs('custody-invoices.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.custody_invoices') }}</span>
            </a>
        @endif

        @if($navUser->canManageUsers())
            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->routeIs('users.*') && !request()->routeIs('users.approvals') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.users') }}</span>
            </a>

            <a href="{{ route('users.approvals') }}"
               class="nav-link {{ request()->routeIs('users.approvals') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.user_approvals') }}</span>
            </a>

            <a href="{{ route('admin-emails.index') }}"
               class="nav-link {{ request()->routeIs('admin-emails.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.admin_emails') }}</span>
            </a>
        @endif

        @if($navUser->canViewAuditLogs())
            <a href="{{ route('audit.index') }}"
               class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.audit_log') }}</span>
            </a>
        @endif

        @if($navUser->canAccessAiAssistantNavigation())
        <a href="{{ route('ai.page') }}"
           class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>{{ __('navigation.ai_assistant') }}</span>
        </a>
        @endif

        <a href="{{ route('technical-support.index') }}"
           class="nav-link {{ request()->routeIs('technical-support.*') || request()->routeIs('support.*') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>{{ __('navigation.technical_support') }}</span>
        </a>

        <a href="{{ route('profile.show') }}"
           class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="nav-link-icon"></span>
            <span>{{ __('navigation.profile') }}</span>
        </a>
        @endif

        @if($navUser->isBasicUser())
            <div class="nav-section-label">{{ __('navigation.services') }}</div>
            <a href="{{ route('leaves.create') }}"
               class="nav-link {{ request()->routeIs('leaves.create') || request()->routeIs('leaves.store') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.leave_request') }}</span>
            </a>
            <a href="{{ route('support.index') }}"
               class="nav-link {{ request()->routeIs('support.*') || request()->routeIs('technical-support.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.technical_support') }}</span>
            </a>
            <a href="{{ route('profile.show') }}"
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="nav-link-icon"></span>
                <span>{{ __('navigation.profile') }}</span>
            </a>
        @endif

        <div class="nav-section-label">{{ __('navigation.language') }}</div>
        <select
            onchange="window.location.href=this.value"
            style="
                width:100%;
                padding:8px;
                border-radius:8px;
                background:#f8fafc;
                color:#111827;
                border:1px solid #000000;
            "
        >
            @foreach(config('locales.supported', ['ar','en','ur']) as $loc)
                <option
                    value="{{ route('locale.switch', $loc) }}"
                    {{ app()->getLocale() === $loc ? 'selected' : '' }}
                >
                    {{ config('locales.labels.'.$loc, strtoupper($loc)) }}
                </option>
            @endforeach
        </select>

        <div class="nav-section-label">{{ __('navigation.session') }}</div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 8px;">
            @csrf
            <button type="submit" class="btn btn-danger" style="width:100%;">
                {{ __('navigation.logout') }}
            </button>
        </form>
    </div>
</aside>
