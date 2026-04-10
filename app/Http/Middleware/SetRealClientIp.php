<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetRealClientIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for Cloudflare header (most reliable)
        $realIp = $request->header('CF-Connecting-IP');
        
        if (!$realIp) {
            // Check True-Client-IP (also set by Cloudflare)
            $realIp = $request->header('True-Client-IP');
        }
        
        if (!$realIp) {
            // Check X-Forwarded-For (Render + Cloudflare)
            $forwardedFor = $request->header('X-Forwarded-For');
            if ($forwardedFor) {
                $ips = explode(',', $forwardedFor);
                $realIp = trim($ips[0]);
            }
        }
        
        // Validate the IP
        if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP)) {
            // Override the server's REMOTE_ADDR with the real client IP
            $request->server->set('REMOTE_ADDR', $realIp);
            $_SERVER['REMOTE_ADDR'] = $realIp;
        }
        
        return $next($request);
    }
}