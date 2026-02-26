<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            !$user ||
            (!($user->is_admin ?? false) && !($user->is_super_admin ?? false))
        ) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        
        // Log admin access
        if (config('logging.admin_access')) {
            \Log::channel('admin')->info('Admin access', [
                'user_id' => $request->user()->id,
                'path' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }
        
        return $next($request);
    }
}
