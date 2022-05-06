<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = 'compliance/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            foreach ($this->centralDomains() as $domain) {
                Route::prefix('api')
                    ->middleware('api')
                    ->domain($domain)
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api.php'));
                if(env('TENANCY_ENABLED')){
                    Route::middleware(['web'])
                    ->domain($domain)
                    ->namespace($this->namespace)
                    ->group(base_path('routes/central.php'));
                }
                else if(env('LICENSE_ENABLED')){
                    Route::middleware('web')->namespace($this->namespace)
                    ->domain($domain)
                    ->group(base_path('routes/license/index.php'));

                    Route::middleware(['web','check.license.activation'])
                    ->domain($domain)
                    ->namespace($this->namespace)
                    ->group(base_path('routes/web.php'));
                }
                else{
                    Route::middleware(['web'])
                    ->domain($domain)
                    ->namespace($this->namespace)
                    ->group(base_path('routes/web.php'));

                     Route::middleware('saml')
                    ->domain($domain)
                    ->namespace($this->namespace)
                        ->group(base_path('routes/saml2/index.php'));

                }
                
                // Route::middleware(['web','check.license.activation'])
                // ->namespace($this->namespace)
                // ->group(base_path('routes/web.php'));

                // Route::middleware('web')->namespace($this->namespace)
                // ->group(base_path('routes/license/index.php'));

                // Route::middleware('saml')
                //     ->domain($domain)
                //     ->namespace($this->namespace)
                //         ->group(base_path('routes/saml2/index.php'));
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    protected function centralDomains(): array
    {
        return config('tenancy.central_domains');
    }
}
