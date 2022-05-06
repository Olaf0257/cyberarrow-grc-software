<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogContextMiddleware
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
        $email = null;
        if (Auth::check()) {
            $email = Auth::user()->email;
        }

        Log::withContext([
            'ip_addr' => $request->ip(),
            'email' => $email
        ]);

        return $next($request);
    }
}
