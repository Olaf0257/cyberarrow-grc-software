<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use LdapRecord\Container;
use App\Nova\Model\Domain;
use App\Nova\Model\Tenant;
use LdapRecord\Connection;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        if(isset($_SERVER['HTTP_HOST']) && env('TENANCY_ENABLED')){
            $domain=Domain::where('domain',$_SERVER['HTTP_HOST'])->get();
            if($domain->count()>0){
                 $tenant=Tenant::where('id',$domain[0]->tenant_id)->first();
                 if($tenant){
                    \Config::set('database.connections.mysql.database', 'tenant'.$tenant->id);
                    \DB::purge('mysql');
                    //  tenancy()->initialize($tenant);
                 }
            }
        }


        Inertia::share('APP_URL', asset('/'));

        try {

            if (\Schema::hasTable('ldap_settings')) {
                $ldapSettings = \DB::table('ldap_settings')->first();

                if ($ldapSettings && $ldapSettings->hosts && $ldapSettings->base_dn && $ldapSettings->username && $ldapSettings->password) {
                    $connection = new Connection([
                        // Mandatory Configuration Options
                        'hosts' => [$ldapSettings->hosts],
                        'base_dn' => $ldapSettings->base_dn,
                        'username' => $ldapSettings->username,
                        'password' => $ldapSettings->password,

                        // Optional Configuration Options
                        'port' => $ldapSettings->port ?: 389,
                        'use_ssl' => (bool) $ldapSettings->use_ssl ?: false,
                        'version' => $ldapSettings->version ?: 3,
                    ]);

                    Container::setDefaultConnection('ldap');

                    Container::addConnection($connection, 'ldap');
                }
            }

            //Global Setting set timezone from database
            if (\Schema::hasTable('account_global_settings')) {
                $globalSettings = \DB::table('account_global_settings')->first();

                if ($globalSettings && $globalSettings->timezone) {
                    \Config::set('app.timezone', $globalSettings->timezone);
                    date_default_timezone_set($globalSettings->timezone);
                }
            }

            // default pagination
            Paginator::useBootstrap();
        } catch (\Exception $exception) {
            \Log::error($exception);
        }
    }
}
