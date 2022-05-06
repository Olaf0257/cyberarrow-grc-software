<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictNovaRouteAccess
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
        $central_domains=explode(',',env('CENTRAL_DOMAINS'));
        $client_domain=$request->getHost();
        $request_url=$request->getPathInfo();
        $is_nova_url=str_contains($request_url,'nova');
        if(!in_array($client_domain,$central_domains) && $is_nova_url){
            abort(404);
        }
        // $client_ip=$_SERVER['REMOTE_ADDR'];
        // $white_listed_ips=explode(',',env('CENTRAL_DOMAIN_ACCESS_IPS'));
        // if(!in_array($client_ip,$white_listed_ips) ){
        //     abort(404);
        // }

        return $next($request);
    }
}
