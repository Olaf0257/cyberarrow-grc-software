<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Admin\ProjectControl' => 'App\Policies\Controls\ProjectControlPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        if(Auth::check('admin')) {
            $admin = Auth::guard('admin')->user();
        
            Gate::before(function ($admin, $ability) {
                return $admin->hasRole('Global Admin') ? true : null;
            });
        }
    }
}
