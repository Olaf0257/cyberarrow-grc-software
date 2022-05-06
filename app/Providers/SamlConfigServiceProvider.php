<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SamlConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
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

                    \Config::set($IdpConfigKey.'entityId', $samlSettings->entity_id);
                    \Config::set($IdpConfigKey.'singleSignOnService.url', $samlSettings->sso_url);
                    \Config::set($IdpConfigKey.'singleLogoutService.url', $samlSettings->slo_url);


                    // setting certificate
                    if ($samlSettings->is_x509certMulti) {
                        $x509certMulti = json_decode($samlSettings->certificate, true);

                        \Config::set($IdpConfigKey.'x509certMulti', $x509certMulti);
                    } else {
                        \Config::set($IdpConfigKey.'x509cert', $samlSettings->certificate);
                    }

                    $isSsoConfigured = true;
                }
            }

            \View::share('isSsoConfigured', $isSsoConfigured);
        } catch (\Exception $exception) {

            \Log::error($exception);
        }
    }
}
