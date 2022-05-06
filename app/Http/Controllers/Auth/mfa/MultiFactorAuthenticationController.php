<?php

namespace App\Http\Controllers\Auth\mfa;

use App\Http\Controllers\Controller;
use App\Utils\RegularFunctions;
use App\Models\Mfa\ResetMfa;
use App\Models\UserManagement\Admin;
use App\Notifications\SendMFAResetlink;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Notification;

class MultiFactorAuthenticationController extends Controller
{
    protected $loggedInUser;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->loggedInUser = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function prepareTwoFactor(Request $request)
    {
        // access control
        if ($this->loggedInUser->hasTwoFactorEnabled()) {
            return RegularFunctions::accessDeniedResponse();
        }

        $secret = $this->loggedInUser->createTwoFactorAuth();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'as_qr_code' => $secret->toQr(),     // As QR Code
                    'as_uri' => $secret->toUri(),    // As "otpauth://" URI.
                    'as_string' => $secret->toString(), // As a string
                ],
            ]);
        } else {
            return view('auth.mfa.setup-mfa', [
                'as_qr_code' => $secret->toQr(),     // As QR Code
                'as_uri' => $secret->toUri(),    // As "otpauth://" U   RI.
                'as_string' => $secret->toString(), // As a string
            ]);
        }
    }

    public function validateMfaCode(Request $request)
    {
        // access control
        if ($this->loggedInUser->hasTwoFactorEnabled()) {
            return RegularFunctions::accessDeniedResponse();
        }

        $request->validate([
            'two_factor_code' => 'required',
        ]);

        if (
            hash_equals(
                $this->loggedInUser->twoFactorAuth->makeCode('now'),
                $request->get('two_factor_code')
            )
        ) {
            return 'true';
        }

        return 'false';
    }

    public function confirmTwoFactor(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required',
        ]);

        // access control
        if ($this->loggedInUser->hasTwoFactorEnabled()) {
            return RegularFunctions::accessDeniedResponse();
        }

        $result = $this->loggedInUser->confirmTwoFactorAuth(
            $request->input('two_factor_code')
        );

        if ($result) {
            $title = 'Success!';
            $message = 'MFA Successfully enabled!';
        } else {
            $title = 'Oops!';
            $message = 'Oops! something went wrong, please redo the MFA setup';
        }

        if (!request()->during_login) {
            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $title,
                    'message' => $message,
                ],
            ]);
        } else {
            $data = [
                'pageTitle' => 'MFA Enabled Successfully',
                'title' => 'Success!',
                'message' => 'MFA Enabled Successfully!',
                'actionLink' => route('admin-user-management-edit', $this->loggedInUser->id),
                'actionTitle' => 'Back To Profile',
            ];

            return inertia('auth/StatusPage', compact('data'));
            // return view('pages.alert-page', compact('data'));
        }
    }

    public function resetTwoFactorAuth(Request $request)
    {
        // access control
        if (!$this->loggedInUser->hasTwoFactorEnabled()) {
            return RegularFunctions::accessDeniedResponse();
        }

        $result = $this->loggedInUser->twoFactorAuth->flushAuth()->save();

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully reset MFA',
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    public function sendMFAResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $email = $request['email'];

        //deleting the row  with same email as the requested email in the table mfa_resets
        ResetMfa::where('email', $email)->delete();

        $token = (string) Str::uuid();

        //creating a instance to save email and token
        $resetMFA = new ResetMfa();
        $resetMFA->email = $email;
        $resetMFA->token = $token;
        $resetMFA->save();

        Notification::route('mail', $email)->notify(new SendMFAResetlink($token));

        $data = [
            'pageTitle' => 'Reset MFA Success',
            'title' => 'Success!',
            'header' => 'We have e-mailed you an MFA Reset link',
            'message' => 'If the provided email is a valid registered email, you will receive an MFA reset link in your inbox',
            'actionLink' => route('admin-logout'),
            'actionTitle' => 'Back To Home',
        ];

        return inertia('auth/StatusPage', compact('data'));
        // return view('pages.messages', compact('data'));
    }

    public function ResetMFA(Request $request, $token)
    {
        $email = ResetMfa::whereToken($token)->pluck('email')->first();

        ResetMfa::where('token', $token)->delete();

        if (!$email) {
            $data = [
                'title' => 'Error!',
                'message' => 'Sorry, Your link to reset Mfa has expired.',
                'action' => true,
            ];

            return inertia('auth/StatusPage', compact('data'));
            // return view('pages.messages', compact('data'));
        }

        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            $data = [
                'title' => 'Error!',
                'message' => 'Sorry we were unable to Reset Your Mfa.Please Try again.',
                'action' => true,
            ];

            return inertia('auth/StatusPage', compact('data'));
            // return view('pages.messages', compact('data'));
        }

        $admin->twoFactorAuth->flushAuth()->save();

        $data = [
            'title' => 'Success!',
            'message' => 'Your MFA has been reset. Please log in again',
            'actionLink' => route('admin-logout'),
        ];

        return inertia('auth/StatusPage', compact('data'));
        // return view('pages.messages', compact('data'));
    }
}
