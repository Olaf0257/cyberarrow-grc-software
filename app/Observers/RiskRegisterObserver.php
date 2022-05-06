<?php

namespace App\Observers;

use App\Models\RiskManagement\RiskMatrix\RiskMatrixImpact;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixLikelihood;
use App\Utils\RegularFunctions;
use App\Mail\RiskManagement\RiskClose;
use App\Models\Compliance\ProjectControl;
use App\Models\RiskManagement\RiskMappedComplianceControl;
use App\Models\RiskManagement\RiskRegister;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixScore;

class RiskRegisterObserver
{

    
    public function creating(RiskRegister $riskRegister)
    {
        $likelihoodCount = RiskMatrixLikelihood::count();
        $impactCount = RiskMatrixImpact::count();

        $is_3x3 = ($likelihoodCount === $impactCount) && ($likelihoodCount === 3);

        /* Setting the default value whe not set*/
        if (!isset($riskRegister->likelihood)) {
            $middleLikelihood = $is_3x3 ? 2 : intval(floor($likelihoodCount / 2));
            $riskRegister->likelihood = $middleLikelihood;
        }

        if (!isset($riskRegister->impact)) {
            $middleImpact = $is_3x3 ? 2 : intval(floor($impactCount / 2));
            $riskRegister->impact = $middleImpact;
        }

        /* When likelihood  and impact index is given*/
        if (isset($riskRegister->likelihood) && isset($riskRegister->impact)) {
            $riskScore = RiskMatrixScore::where('likelihood_index', $riskRegister->likelihood - 1)->where('impact_index', $riskRegister->impact - 1)->first();

            if ($riskScore) {
                $riskRegister->inherent_score = $riskScore->score;
                $riskRegister->residual_score = $riskScore->score;
            }
        }
    }

    /**
     * Handle the Risk control "updating" event.
     *
     * @param \App\Models\Admin\RiskRegister $RiskRegister
     */
    public function updating(RiskRegister $RiskRegister)
    {
        //For accepted risk change status to close and removing all mapped project controls
        if ($RiskRegister->isDirty('treatment_options') && $RiskRegister->treatment_options == 'Accept') {
            $RiskRegister->status = 'Close';
            $mapRisk = RiskMappedComplianceControl::where('risk_id', $RiskRegister->id)->first();
            $implementComplianceControl = '';
            $riskAdminArray = RegularFunctions::getRiskContributorList();

            if ($mapRisk) {
                $implementComplianceControl = ProjectControl::where('id', $mapRisk->control_id)->where('status', 'Implemented')->first();
            }

            $responsibleUsers = [];

            if ($RiskRegister->owner && $RiskRegister->custodian) {
                array_push($responsibleUsers,
                    [
                        'name' => $RiskRegister->owner->full_name,
                        'email' => $RiskRegister->owner->email,
                    ],
                    [
                        'name' => $RiskRegister->custodian->full_name,
                        'email' => $RiskRegister->custodian->email,
                    ]
                );
            } elseif ($implementComplianceControl) {
                array_push($responsibleUsers,
                    [
                        'name' => ucwords($implementComplianceControl->responsibleUser->full_name),
                        'email' => $implementComplianceControl->responsibleUser->email,
                    ],
                    [
                        'name' => ucwords($implementComplianceControl->approverUser->full_name),
                        'email' => $implementComplianceControl->approverUser->email,
                    ]
                );
            }

            $allUserArray = array_merge($riskAdminArray, $responsibleUsers);
            $uniqueUserArray = array_map('unserialize', array_unique(array_map('serialize', $allUserArray)));

            foreach ($uniqueUserArray as $user) {
                $userName = $user['name'];

                if ($implementComplianceControl) {
                    $data = [
                        'greeting' => 'Hello ' . decodeHTMLEntity($userName),
                        'content1' => 'The below risk has been closed.',
                        'content2' => '<b style="color: #000000;">Risk Name: </b> ' . decodeHTMLEntity($RiskRegister->name),
                        'content3' => '<b style="color: #000000;">Control: </b> ' . decodeHTMLEntity($implementComplianceControl->name),
                        'content4' => '<b style="color: #000000;">Risk Treatment: </b> ' . decodeHTMLEntity($RiskRegister->treatment_options),
                        'content5' => '<b style="color: #000000;">Status: </b> Closed',
                        'content6' => 'No further action is needed.',
                    ];
                } else {
                    $data = [
                        'greeting' => 'Hello ' . decodeHTMLEntity($userName),
                        'content1' => 'The below risk has been closed.',
                        'content2' => '<b style="color: #000000;">Risk Name: </b> ' . decodeHTMLEntity($RiskRegister->name),
                        'content4' => '<b style="color: #000000;">Risk Treatment: </b> ' . decodeHTMLEntity($RiskRegister->treatment_options),
                        'content5' => '<b style="color: #000000;">Status: </b> Closed',
                        'content6' => 'No further action is needed.',
                    ];
                }

                \Mail::to($user['email'])->send(new RiskClose($data));
            }
            $mapRisk = RiskMappedComplianceControl::where('risk_id', $RiskRegister->id)->delete();
        }

        //For Mitigate risk change status to open
        if ($RiskRegister->isDirty('treatment_options') && $RiskRegister->treatment_options == 'Mitigate') {
            $RiskRegister->status = 'Open';
        }
    }
}
