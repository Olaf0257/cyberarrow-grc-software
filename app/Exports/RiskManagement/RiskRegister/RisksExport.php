<?php

namespace App\Exports\RiskManagement\RiskRegister;

use App\Models\RiskManagement\RiskRegister;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class RisksExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $risks = RiskRegister::with('controls', 'controls.responsibleUser', 'controls.approverUser', 'owner', 'custodian')->get();

        $excelCollection = [
            [
                'Risk ID',
                'Name',
                'Description',
                'Affected function(s)/asset(s)',
                'Affected property(ies)',
                'Likelihood',
                'Impact',
                'Inherent Risk Score',
                'Treatment Option',
                'Control',
                'Treatment Description',
                'Risk Custodian',
                'Risk Owner',
                'Treatment Due Date',
                'Status',
                'Residual Risk Score',
                'Risk Value',
            ],
        ];

        $i = 0;
        foreach ($risks as $risk) {
            $mappedControl = $risk->controls()->first();

            $mappedControlResponsibeUserFullName = $mappedControl ? ($mappedControl->responsible ? $mappedControl->responsibleUser->full_name : '') : '';
            $mappedControlApproverUserFullName = $mappedControl ? ($mappedControl->approver ? $mappedControl->approverUser->full_name : '') : '';

            $risk_owner = $risk->owner ? $risk->owner->full_name : null;
            $risk_owner = $risk_owner ?: $mappedControlResponsibeUserFullName;

            $risk_custodian = $risk->custodian? $risk->custodian->full_name : null;
            $risk_custodian = $risk_custodian ?: $mappedControlApproverUserFullName;

            $excelCollection[] = [
                ++$i,
                $risk->name,
                $risk->risk_description,
                $risk->affected_functions_or_assets,
                $risk->affected_properties,
                $risk->likelihood,
                $risk->impact,
                $risk->inherent_score,
                $risk->treatment_options,
                $mappedControl ? $mappedControl->name : '',
                $risk->treatment,
                $risk_custodian,
                $risk_owner,
                $mappedControl ? $mappedControl->deadline : '',
                $risk->status,
                $risk->residual_score,
                $risk->ResidualRiskScoreLevel ? $risk->ResidualRiskScoreLevel->name : '',
            ];
        }

        return new Collection(
            $excelCollection
        );
    }
}
