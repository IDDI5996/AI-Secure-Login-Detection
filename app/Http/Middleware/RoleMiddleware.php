<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
         if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role ?? 'user';
        
        // Super admin has access to everything
        if ($request->user()->is_super_admin) {
            return $next($request);
        }

        // Check if user has required role
        if (!in_array($userRole, $roles) && !$request->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
