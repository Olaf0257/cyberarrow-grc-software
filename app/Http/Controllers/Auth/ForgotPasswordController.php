<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\Admin;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function forgetPasswordForm()
    {
        return inertia('auth/ForgetPassword');
        return view('auth.forget-password');
    }

    public function broker()
    {
        return Password::broker('admins');
    }

    public function forgetPasswordVerifyEmail(Request $request)
    {
        if ($request->email) {
            $admin = Admin::where('email', $request->email)->first();

            if ($admin) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', function ($attribute, $value, $fail) use ($request) {
                $admin = Admin::where('email', $request->email)->first();
                if ($admin) {
                    if ($admin->status != 'active') {
                        $fail('Account with this ' . $attribute . ' is ' . $admin->status);
                    }

                    if ($admin->auth_method == 'LDAP') {
                        $fail('Accounts created with LDAP server are not allowed to reset password');
                    }
                } else {
                    $fail('The email address doesn\'t exist.');
                }
            }],
        ]);
    }
    protected function sendResetLinkResponse(Request $request, $response)
    {
        $data = [
            'pageTitle' => 'Forgot Password Success',
            'title' => 'Success!',
            'message' => 'If the provided email is a valid registered email, you will receive a password reset link in your inbox.',
            'actionLink' => route('login'),
            'actionTitle' => 'Back To Home',
        ];

        return inertia('auth/StatusPage', compact('data'));
        return view('pages.messages', compact('data'));
    }
}
