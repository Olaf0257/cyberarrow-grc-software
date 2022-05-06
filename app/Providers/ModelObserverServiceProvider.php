<?php

namespace App\Providers;

use App\Models\Administration\OrganizationManagement\Department;
use App\Models\Compliance\ProjectControl;
use App\Models\Compliance\Standard;
use App\Models\GlobalSettings\GlobalSetting;
use App\Models\RiskManagement\RiskRegister;
use App\Observers\Compliance\ProjectControlObserver;
use App\Observers\DepartmentObserver;
use App\Observers\GlobalSettings\GlobalSettingObserver;
use App\Observers\RiskRegisterObserver;
use App\Observers\StandardObserver;
use Illuminate\Support\ServiceProvider;

class ModelObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        Standard::observe(StandardObserver::class);
        ProjectControl::observe(ProjectControlObserver::class);
        GlobalSetting::observe(GlobalSettingObserver::class);
        RiskRegister::observe(RiskRegisterObserver::class);
        Department::observe(DepartmentObserver::class);
    }
}
