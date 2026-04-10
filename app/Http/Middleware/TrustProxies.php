<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var string|array
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int|string
     */
    protected $headers = '*';

    /**
     * Get the client IP address from the request, checking Cloudflare/Render headers first.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function getIp(Request $request)
    {
        // Check for Cloudflare's CF-Connecting-IP header (most reliable)
        $ip = $request->header('CF-Connecting-IP');
        
        if (!$ip) {
            // Check for True-Client-IP header (also set by Cloudflare)
            $ip = $request->header('True-Client-IP');
        }
        
        if (!$ip) {
            // Check X-Forwarded-For header (Render + Cloudflare)
            $forwardedFor = $request->header('X-Forwarded-For');
            if ($forwardedFor) {
                // The first IP in the list is the original client
                $ips = explode(',', $forwardedFor);
                $ip = trim($ips[0]);
            }
        }
        
        if (!$ip) {
            // Fall back to Laravel's default IP detection
            $ip = parent::getIp($request);
        }
        
        // Validate IP format (basic)
        if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
        
        return null;
    }
}