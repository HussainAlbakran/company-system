<?php

namespace App\Providers;

use App\Models\InstallationFactoryRequest;
use App\Models\Project;
use App\Policies\ProjectPolicy;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ConsoleKernelContract::class, \App\Console\Kernel::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);

        View::composer('layouts.app', function ($view): void {
            if (! auth()->check() || ! auth()->user()->canManageProduction()) {
                return;
            }

            $pendingInstallationFactoryRequestsCount = InstallationFactoryRequest::query()
                ->where('status', InstallationFactoryRequest::STATUS_SUBMITTED)
                ->count();

            $view->with('pendingInstallationFactoryRequestsCount', $pendingInstallationFactoryRequestsCount);
        });
    }
}
