<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next, string $locale = 'en'): Response
    {
        App::setLocale(in_array($locale, ['en', 'mr'], true) ? $locale : 'en');

        return $next($request);
    }
}
