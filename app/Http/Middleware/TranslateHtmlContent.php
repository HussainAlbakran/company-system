<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslateHtmlContent
{
    public function handle(Request $request, Closure $next): Response
    {
        // UI copy is translated via __() / @lang in Blade and lang/* files.
        // Avoid post-processing HTML so locales (ar / en / ur) stay consistent.
        return $next($request);
    }
}

