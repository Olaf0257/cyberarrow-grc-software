<?php

namespace App\Observers\GlobalSettings;

use App\Models\GlobalSettings\GlobalSetting;
use App\Models\UserManagement\Admin;

class GlobalSettingObserver
{
    /**
     * Handle the global setting "created" event.
     *
     * @param  \App\Admin\AccountSetting\GlobalSetting  $globalSetting
     * @return void
     */
    public function created(GlobalSetting $globalSetting)
    {
        //
    }

    /**
     * Handle the global setting "updated" event.
     *
     * @param  \App\Admin\AccountSetting\GlobalSetting  $globalSetting
     * @return void
     */
    public function updated(GlobalSetting $globalSetting)
    {
        if ($globalSetting->isDirty('secure_mfa_login')) {
            if ($globalSetting->secure_mfa_login) {
                Admin::query()->update(['require_mfa' => 1]);
            } else {
                Admin::query()->update(['require_mfa' => 0]);
            }
        }
    }

    /**
     * Handle the global setting "deleted" event.
     *
     * @param  \App\Admin\AccountSetting\GlobalSetting  $globalSetting
     * @return void
     */
    public function deleted(GlobalSetting $globalSetting)
    {
        //
    }

    /**
     * Handle the global setting "restored" event.
     *
     * @param  \App\Admin\AccountSetting\GlobalSetting  $globalSetting
     * @return void
     */
    public function restored(GlobalSetting $globalSetting)
    {
        //
    }

    /**
     * Handle the global setting "force deleted" event.
     *
     * @param  \App\Admin\AccountSetting\GlobalSetting  $globalSetting
     * @return void
     */
    public function forceDeleted(GlobalSetting $globalSetting)
    {
        //
    }
}
