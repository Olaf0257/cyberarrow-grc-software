<?php

namespace App\Http\Controllers\Auth\mfa;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Admin;
use App\Utils\RegularFunctions;
use DarkGhostHunter\Laraguard\Contracts\TwoFactorAuthenticatable;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Contracts\Config\Repository;
use DarkGhostHunter\Laraguard\Listeners\ChecksTwoFactorCode;
use Illuminate\Http\Request;

class LoginConfirmMFACodeController extends Controller
{
    use ChecksTwoFactorCode;

    /**
     * Config repository.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Current Request being handled.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Input name to verify Two Factor Code presence.
     *
     * @var string
     */
    protected $input;

    /**
     * Credentials used for Login in.
     *
     * @var array
     */
    protected $credentials;

    /**
     * If the user should be remembered.
     *
     * @var bool
     */
    protected $remember;

    /**
     * Create a new Subscriber instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Repository $config, Request $request)
    {
        $this->middleware(['throttle:60,1'])->only('confirm');
        $this->config = $config;
        $this->request = $request;
        $this->input = $config['laraguard.input'];
    }

    /**
     * Saves the credentials temporarily into the class instance.
     *
     * @param  \Illuminate\Auth\Events\Attempting  $event
     * @return void
     */
    public function saveCredentials(Attempting $event)
    {
        $this->credentials = (array) $event->credentials;
        $this->remember = (bool) $event->remember;
    }

    /**
     * Display the TOTP code confirmation view.
     *
     * @return \Illuminate\View\View
     */
    public function showConfirmForm()
    {
        $email = session()->get('email');
        return inertia('auth/mfa/ConfirmMFA', compact('email'));
        // return view('laraguard::auth', [
        //     'action'      => $this->request->fullUrl(),
        //     'credentials' => $this->credentials,
        //     // 'user'        => $user,
        //     // 'error'       => $error,
        //     'remember'    => $this->remember,
        //     'input'       => $this->input
        // ]);
    }

    /**
     * Confirm the given user's TOTP code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        RegularFunctions::set_db();
        $user = Admin::where('email', $request->email)->first();

        if ($user instanceof TwoFactorAuthenticatable) {
            // If the user has set an invalid code, throw him a response.
            if (!$this->hasValidCode($user)) {
                return back()->with('error', 'The Code is invalid or has expired.');
            }

            // The code is valid so we will need to check if the device should
            // be registered as safe. For that, we will check if the config
            // allows it, and there is a checkbox filled to opt-in this.
            if ($this->isSafeDevicesEnabled() && $this->wantsAddSafeDevice()) {
                $user->addSafeDevice($this->request);
            }

            $this->resetTotpConfirmationTimeout($request);

            RegularFunctions::unset_db();

            return $request->wantsJson()
                ? response()->noContent()
                : redirect()->intended($this->redirectPath());
        }
        return back()->with('error', 'Invalid User.');
    }

    /**
     * Reset the TOTP code timeout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function resetTotpConfirmationTimeout(Request $request)
    {
        $request->session()->put('2fa.totp_confirmed_at', now()->timestamp);
    }

    /**
     * Get the TOTP code validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            config('laraguard.input') => 'required|totp_code',
        ];
    }

    /**
     * Get the password confirmation validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Return the path to redirect if no intended path exists.
     *
     * @return string
     * @see \Illuminate\Foundation\Auth\RedirectsUsers
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
