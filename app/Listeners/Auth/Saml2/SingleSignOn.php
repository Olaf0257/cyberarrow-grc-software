<?php

namespace App\Listeners\Auth\Saml2;

use App\Events\Auth\Saml2\Saml2LoginEvent;
use App\Models\UserManagement\Admin;
use Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SingleSignOn
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {
        $idpUser = $event->getSaml2User();

        $user = Admin::where('email', $idpUser->getUserId())->first();

        $errorsMsgs = [];

        if (!$user) {
            $errorsMsgs['email'] = ['Incorrect username/password'];

            throw ValidationException::withMessages($errorsMsgs);
        }

        if ($user->status == 'unverified') {
            $errorsMsgs['email'] = ['Email not verified'];
        } elseif ($user->status == 'disabled') {
            $errorsMsgs['email'] = ['User with this email has been disabled'];
        }

        if (count($errorsMsgs) > 0) {
            throw ValidationException::withMessages($errorsMsgs);
        }

        // authenticating the user
        Auth::guard('admin')->login($user);
        $user->is_sso_auth = 1;
        $user->last_login = Carbon::now();
        $user->update();
    }
}
