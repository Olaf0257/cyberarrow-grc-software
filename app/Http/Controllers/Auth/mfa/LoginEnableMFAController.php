<?php

namespace App\Http\Controllers\Auth\mfa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginEnableMFAController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->loggedInUser = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function showEnableMFAPage()
    {
        return inertia('auth/mfa/EnableMFA');
        return view('laraguard::notice');
    }

    public function showSetupMFAPage()
    {
        $secret = $this->loggedInUser->createTwoFactorAuth();

        return inertia('auth/mfa/SetupMFA', [
            'QRCode' => $secret->toQr(),     // As QR Code
            'URI' => $secret->toUri(),    // As "otpauth://" URI.
            'secretToken' => $secret->toString(), // As a string
        ]);
    }
}
