<?php

namespace App\Observers;

use App\Models\Compliance\Standard;

class StandardObserver
{
    /**
     * Handle the standard "created" event.
     *
     * @param \App\Models\Admin\Standard $standard
     */
    public function created(Standard $standard)
    {
    }

    /**
     * Handle the standard "updated" event.
     *
     * @param \App\Models\Admin\Standard $standard
     */
    public function updated(Standard $standard)
    {
    }

    /**
     * Handle the standard "deleting" event.
     *
     * @param \App\Models\Admin\Standard $standard
     */
    public function deleting(Standard $standard)
    {
        $standard->controls()->delete();
    }

    /**
     * Handle the standard "deleted" event.
     *
     * @param \App\Models\Admin\Standard $standard
     */
    public function deleted(Standard $standard)
    {
    }

    /**
     * Handle the standard "restored" event.
     *
     * @param \App\Models\Admin\Standard $standard
     */
    public function restored(Standard $standard)
    {
    }

    /**
     * Handle the standard "force deleted" event.
     *
     * @param \App\Models\Admin\Standard $standard
     */
    public function forceDeleted(Standard $standard)
    {
    }
}
