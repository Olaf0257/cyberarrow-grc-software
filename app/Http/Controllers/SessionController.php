<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use App\Models\GlobalSettings\GlobalSetting;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function ajaxCheck()
    {
        $globalSetting = GlobalSetting::first();

        if (Auth::guard('admin')->check() && !is_null($globalSetting->session_timeout)) {
            // Log out user if idle for too long
            if (time() - Session::get('lastActivityTime') > $globalSetting->session_timeout * 60) {
                Session::forget('lastActivityTime');

                $user = Auth::guard('admin')->user();

                Auth::guard('admin')->logout();

                request()->session()->invalidate();

                request()->session()->regenerateToken();

                return response()->json([
                    'success' => true,
                    'user' => $user,
                ]);
            }
        }

        return response()->json([
            'success' => false,
        ]);
    }

    public function showPagesLockScreen(Request $request)
    {
        $email = $request->email;
        $fullName = $request->fullName;
        $loggedInWithSSO = $request->loggedInWithSSO ? 'yes' : 'no'; // faced problems when sending boolean value in react component

        return inertia('auth/SessionOutPage', compact('email', 'fullName', 'loggedInWithSSO'));
        // return view('pages.pages-lock-screen', compact('email', 'full_name', 'loggedInWithSSO'));
    }

    public function getSAMLConfiguration()
    {
        try {
            $isSsoConfigured = false;

            if (\Schema::hasTable('saml_settings')) {
                $samlSettings = \DB::table('saml_settings')->first();

                if (
                    $samlSettings
                    && isset($samlSettings->sso_provider)
                    && isset($samlSettings->entity_id)
                    && isset($samlSettings->sso_url)
                    && isset($samlSettings->slo_url)
                    && isset($samlSettings->certificate)
                ) { //checking if table is not empty
                    $IdpConfigKey = 'saml2.ebdaa_idp_settings.idp.';

                    \Config::set($IdpConfigKey . 'entityId', $samlSettings->entity_id);
                    \Config::set($IdpConfigKey . 'singleSignOnService.url', $samlSettings->sso_url);
                    \Config::set($IdpConfigKey . 'singleLogoutService.url', $samlSettings->slo_url);

                    // setting certificate
                    if ($samlSettings->is_x509certMulti) {
                        $x509certMulti = json_decode($samlSettings->certificate, true);

                        \Config::set($IdpConfigKey . 'x509certMulti', $x509certMulti);
                    } else {
                        \Config::set($IdpConfigKey . 'x509cert', $samlSettings->certificate);
                    }

                    $isSsoConfigured = true;
                }
            }

            return response()->json(
                $isSsoConfigured,
                200
            );
        } catch (\Exception $exception) {

            \Log::error($exception);
        }
    }
}
