<?php

use App\Http\Middleware\EnsureAdminFinanceAccess;
use App\Http\Middleware\EnsureHrModuleAccess;
use App\Http\Middleware\RestrictFinanceUserAccess;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Http\Middleware\RestrictBasicUserAccess;
use App\Http\Middleware\AuditRequestActivity;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TranslateHtmlContent;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            TranslateHtmlContent::class,
            AuditRequestActivity::class,
        ]);

        $middleware->alias([
            'approved' => EnsureUserIsApproved::class,
            'role' => RoleMiddleware::class,
            'hr.module' => EnsureHrModuleAccess::class,
            'admin.finance' => EnsureAdminFinanceAccess::class,
            'basic_user_restricted' => RestrictBasicUserAccess::class,
            'finance.restricted' => RestrictFinanceUserAccess::class,
            'set_locale' => SetLocale::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
