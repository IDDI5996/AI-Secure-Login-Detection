<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LecturerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || !($user->role === 'lecturer' || $user->is_admin || $user->is_super_admin)) {
            abort(403, 'Unauthorized – Lecturer access required.');
        }
        return $next($request);
    }
}