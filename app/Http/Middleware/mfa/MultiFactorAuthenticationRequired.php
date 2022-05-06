<?php

namespace App\Http\Middleware\mfa;

use Closure;

class MultiFactorAuthenticationRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $redirectToRoute = '2fa.notice')
    {
        $user = $request->user();

        session()->put('email', $user->email); //For 2FA Login

        if ($user->hasMfaRequired() && ! $user->hasTwoFactorEnabled()) {
            return $request->expectsJson()
                ? abort(403, __('Two Factor Authentication is not enabled.'))
                : redirect()->route($redirectToRoute);
        }

        return $next($request);
    }
}
