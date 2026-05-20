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
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login')
                ->with('error', 'Please sign in to access the admin panel.');
        }

        if (!$user->isStaff() || !$user->is_active) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
