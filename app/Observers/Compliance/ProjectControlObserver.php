<?php

namespace App\Observers\Compliance;

use App\Mail\RiskManagement\RiskClose;
use App\Models\Compliance\ProjectControl;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixAcceptableScore;
use App\Utils\RegularFunctions;

class ProjectControlObserver
{
    /**
     * Handle the project control "created" event.
     *
     * @param \App\Models\Admin\ProjectControl $projectControl
     */
    public function created(ProjectControl $projectControl)
    {
    }

    /**
     * Handle the project control "updating" event.
     *
     * @param \App\Models\Admin\ProjectControl $projectControl
     */
    public function updating(ProjectControl $projectControl)
    {
    }

    /**
     * Handle the project control "updated" event.
     *
     * @param \App\Models\Admin\ProjectControl $projectControl
     */
    public function updated(ProjectControl $projectControl)
    {
        $mappedRisks = $projectControl->risks()->get();

        if ($projectControl->isDirty('status') && $mappedRisks->count() > 0) {

            if ($projectControl->status == 'Implemented') {

                /* Setting residual risk score to acceptable score */
                foreach ($mappedRisks as $mappedRisk) {
                    RegularFunctions::notifyRiskContributorList($projectControl, $mappedRisk);
                }
            } elseif ($projectControl->status == 'Not Implemented') {
                foreach ($mappedRisks as $mappedRisk) {
                    $mappedRisk->status = 'Open';
                    $mappedRisk->treatment_options = 'Mitigate';
                    $mappedRisk->residual_score = $mappedRisk->inherent_score;
                    $mappedRisk->update();
                }
            }
        }
    }

    /**
     * Handle the project control "deleted" event.
     *
     * @param \App\Models\Admin\ProjectControl $projectControl
     */
    public function deleted(ProjectControl $projectControl)
    {
    }

    /**
     * Handle the project control "restored" event.
     *
     * @param \App\Models\Admin\ProjectControl $projectControl
     */
    public function restored(ProjectControl $projectControl)
    {
    }

    /**
     * Handle the project control "force deleted" event.
     *
     * @param \App\Models\Admin\ProjectControl $projectControl
     */
    public function forceDeleted(ProjectControl $projectControl)
    {
    }
}
