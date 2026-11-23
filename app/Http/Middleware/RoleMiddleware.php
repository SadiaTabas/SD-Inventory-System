<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->status != 1) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        // Super Admin has unrestricted access to everything
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // If 'admin' is in the allowed roles, permit any admin staff
        if (in_array('admin', $roles) && $user->isAdmin()) {
            return $next($request);
        }

        // Direct role match
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'You are not authorized to access this page.');
    }
}
