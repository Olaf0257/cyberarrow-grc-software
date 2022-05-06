<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ResetLastActive
{
    /**
     * Instance of Session Store
     * @var session
     */
    protected $session;

    public function __construct(Store $session){
        $this->session        = $session;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(! $this->session->has('lastActivityTime'))
        {
            $this->session->put('lastActivityTime', time());
        }

        $this->session->put('lastActivityTime',time());

        return $next($request);
    }
}
