<?php

namespace App\Http\Middleware;

use App\Nova\Model\Tenant;
use Closure;
use Illuminate\Http\Request;

class CheckTeanantStatus
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
        $tenant=Tenant::where('id',tenant('id'))->first();
        if($tenant->ready){
            return $next($request);
        }
        abort(503,'We are building your site. Please check after some time.');
    }
}
