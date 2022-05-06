<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IpAccessHandler
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
        $client_ip=$_SERVER['REMOTE_ADDR'];
        $white_listed_ips=explode(',',env('CENTRAL_DOMAIN_ACCESS_IPS'));
        if(in_array($client_ip,$white_listed_ips) ){
            return $next($request);
        }
        else{
            abort(403, 'Unauthorized action.');
        }

    }
}
