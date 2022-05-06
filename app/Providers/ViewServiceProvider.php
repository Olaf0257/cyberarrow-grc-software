<?php

namespace App\Providers;

use App\Models\Administration\OrganizationManagement\Organization;
use App\Models\GlobalSettings\GlobalSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (\Schema::hasTable('account_global_settings')) {
            $globalSetting = GlobalSetting::first();

            \View::share('globalSetting', $globalSetting);

            Inertia::share('globalSetting', $globalSetting);
        }

        /* Sharing top level data scope */
        if (\Schema::hasTable('organizations')) {
            $organization = Organization::first();

            $topLevelDataScope = $organization ? ["value" => $organization->id.'-0',"label" => $organization->name] : '';
            \View::share('topLevelDataScope', $topLevelDataScope);
        }

        view()->composer('*', function ($view) {
            if (Auth::guard('admin')->check()) {
                $view->with('loggedInUser', Auth::guard('admin')->user());
            }
        });
    }
}
