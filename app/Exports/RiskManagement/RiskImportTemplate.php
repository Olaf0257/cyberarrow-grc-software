<?php

namespace App\Exports\RiskManagement;

use Maatwebsite\Excel\Concerns\WithHeadings;

class RiskImportTemplate implements WithHeadings
{
    public function headings(): array
    {
        $headingArray = [
            'name',
            'risk_description',
            'affected_properties',
            'affected_functions_or_assets',
            'treatment',
            'category',
            'treatment_options',
            'likelihood',
            'impact',
        ];

        return $headingArray;
    }
}
