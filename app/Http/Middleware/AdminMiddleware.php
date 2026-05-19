<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Protect admin routes.
     * Requires auth AND admin role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please sign in to access the admin panel.');
        }

        // Check admin role — adapt field name to your User model
        if (!in_array($request->user()->role ?? '', ['superadmin', 'admin', 'secretary', 'doctor'])) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
