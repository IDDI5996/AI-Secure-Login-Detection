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
    public function ip()
{
    // Check for Cloudflare header
    $ip = $this->header('CF-Connecting-IP');
    
    if (!$ip) {
        $ip = $this->header('True-Client-IP');
    }
    
    if (!$ip) {
        $forwardedFor = $this->header('X-Forwarded-For');
        if ($forwardedFor) {
            $ips = explode(',', $forwardedFor);
            $ip = trim($ips[0]);
        }
    }
    
    if (!$ip) {
        $ip = parent::ip();
    }
    
    return $ip;
}
}