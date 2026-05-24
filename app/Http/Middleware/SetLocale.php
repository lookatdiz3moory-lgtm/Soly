<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Apply the visitor's preferred locale to the current request.
 *
 * Order of resolution:
 *   1. session('locale') if it's one of config('app.supported_locales')
 *   2. config('app.locale') (default English)
 *
 * The locale switcher route writes to the session and redirects back,
 * so the next request picks up the new locale here.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['en']);
        $locale    = session('locale');

        if (!is_string($locale) || !in_array($locale, $supported, true)) {
            $locale = config('app.locale', 'en');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
