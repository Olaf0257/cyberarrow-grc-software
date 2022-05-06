<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GlobalSettings\GlobalSetting;
use App\Models\GlobalSettings\LdapSetting;
use App\Providers\RouteServiceProvider;
use App\Models\UserManagement\Admin;
use App\Models\UserManagement\LdapUser;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use LdapRecord\Container;
use App\Utils\RegularFunctions;
use DarkGhostHunter\Laraguard\Laraguard;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |ßßß
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest:admin')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return inertia('auth/LoginPage');
        // return view('auth.login');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['status'] = 'active';
        $credentials['email'] = $request->email;

        return $credentials;
    }

    /**
     * Validate the user login request.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|email',
            'password' => 'required|string',
        ]);
    }

    /**
     * The user has been authenticated.
     *
     * @param mixed $user
     *
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user->last_login = Carbon::now();
        $user->update();

        return redirect()->intended(RegularFunctions::getRoleBasedRedirectPath());
    }

    /**
     * Attempt to log the user into the application.
     *
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $user = Admin::where('email', $request->email)->first();

        session()->put('email', $request->email); //For 2FA Login

        $errorsMsgs = [];

        if (!$user) {
            $errorsMsgs['email'] = ['Incorrect Email/Password'];

            Log::info('User login failed. Incorrect Email/Password', [
                'email' => $request->email,
            ]);
            throw ValidationException::withMessages($errorsMsgs);
        }

        if ($user->status == 'unverified') {
            $errorsMsgs['email'] = ['Email not verified'];
        } elseif ($user->status == 'disabled') {
            $errorsMsgs['email'] = ['User with this email has been disabled'];
        }

        if (count($errorsMsgs) > 0) {
            $logMessage = $errorsMsgs['email'][0];
            Log::info("User login failed. $logMessage", [
                'email' => $request->email,
            ]);
            throw ValidationException::withMessages($errorsMsgs);
        }

        switch ($user->auth_method) {

            case 'Manual':
                if ($user->auth_method == 'Manual') {
                    $result = $this->guard()->attempt(
                        $this->credentials($request),
                        $request->filled('remember')
                    );

                    if ($result) {
                        // setting sso auth to false
                        $user->is_sso_auth = 0;
                        $user->update();
                    }

                    return $result;
                }
                break;
            case 'LDAP':
                $ldapSetting = LdapSetting::first();
                if (is_null($ldapSetting)) {
                    return redirect(route('login'))->with('error', 'LDAP Settings not defined');
                }
                $ldapUser = LdapUser::where($ldapSetting->map_email_to, $request->email)->first();

                if (is_null($ldapUser)) {
                    return redirect(route('login'))->with('error', 'LDAP User not found');
                }
                $connection = Container::getDefaultConnection();

                if ($connection->auth()->attempt($ldapUser->getDn(), $request->password)) {
                    // setting sso auth to false
                    $user->is_sso_auth = 0;
                    $user->update();

                    $this->guard()->login($user);

                    return redirect(RegularFunctions::getRoleBasedRedirectPath());
                }
                break;
            case 'SSO':
                if (!$user->password) {
                    $errorsMsgs['email'] = ['Incorrect Email/Password'];
                    $logMessage = $errorsMsgs['email'][0];
                    Log::info("User login failed. $logMessage", [
                        'email' => $request->email,
                    ]);
                    throw ValidationException::withMessages($errorsMsgs);
                }

                $result = $this->guard()->attempt(
                    $this->credentials($request),
                    $request->filled('remember')
                );

                return $result;

            default:
                # code...
                break;
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request, $errorsMsgs = [])
    {
        if ($request->only('session_timeout')) {
            $errorsMsgs['password'] = ['Incorrect Password'];
        } else {
            $errorsMsgs['password'] = ['Incorrect Email/Password'];
        }

        throw ValidationException::withMessages($errorsMsgs);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $previous_session = $this->guard()->user()->session_id;

        if ($previous_session) {
            Session::getHandler()->destroy($previous_session);
        }

        $this->guard()->user()->session_id = Session::getId();
        $this->guard()->user()->save();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended(RegularFunctions::getRoleBasedRedirectPath());
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }
}
