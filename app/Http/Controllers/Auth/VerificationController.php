<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\VerifyUser;
use App\Providers\RouteServiceProvider;
use App\Rules\Admin\Auth\StrongPassword;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin')->except('verifyEmailAndSetPasswordShowForm', 'verifyEmailAndSetPassword');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verifyEmailAndSetPasswordShowForm(Request $request, $token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (!isset($verifyUser)) {
            $title = 'Ooops !';
            $message = 'This link has expired.';

            $data = [
                'title' => $title,
                'message' => $message,
            ];

            return inertia('auth/StatusPage', compact('data'));
            // return view('pages.messages', compact('data'));
        } else {
            $user = $verifyUser->user;

            if ($user->status == 'active') {
                $title = 'Ooops !';
                $message = 'Your e-mail is already verified..';

                $data = [
                    'title' => $title,
                    'message' => $message,
                ];

                return inertia('auth/StatusPage', compact('data'));
                // return view('pages.messages', compact('data'));
            } elseif ($user->status == 'disabled') {
                $title = 'Ooops !';
                $message = 'Your account is disabled..';

                $data = [
                    'title' => $title,
                    'message' => $message,
                ];

                return inertia('auth/StatusPage', compact('data'));
                // return view('pages.messages', compact('data'));
            } else {
                return inertia('auth/VerifyEmailAndSetPassword', compact('token'));
                return view('auth.verify-email-and-set-password', compact('token'));
            }
        }
    }

    public function verifyEmailAndSetPassword(Request $request, $token)
    {
        $this->validate($request, [
            'password' => ['required', 'confirmed', new StrongPassword()],
        ]);

        $verifyUser = VerifyUser::where('token', $token)->first();

        if (isset($verifyUser)) {
            $user = $verifyUser->user;

            if ($user->status == 'unverified') {
                $user->status = 'active';
                $user->password = bcrypt($request->password);
                $user->save();

                $title = 'Success!';
                $message = 'Your account has been verified. You can now login.';
            } else {
                $title = 'Ooops !';
                $message = 'Your e-mail is already verified.';
            }
        } else {
            $title = 'Ooops !';
            $message = 'Sorry your email cannot be identified.';
        }

        $data = [
            'pageTitle' => 'Email Verification Success',
            'title' => $title,
            'message' => $message,
            'actionLink' => route('login'),
            'actionTitle' => 'Back To Login',
        ];

        return inertia('auth/StatusPage', compact('data'));
        // return view('pages.messages', compact('data'));
    }
}
