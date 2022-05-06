<?php

namespace App\Imports\RiskManagement;

use App\Models\RiskManagement\RiskCategory;
use App\Models\RiskManagement\RisksTemplate;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RiskTemplateImport implements ToCollection, WithHeadingRow, WithEvents
{/*
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    use Importable;
    use RegistersEventListeners;

    private $standard;

    public function __construct($standard)
    {
        $this->standard = $standard;
    }

    public function collection(Collection $rows)
    {
        $standard = $this->standard;

        //Getting all risk category
        $riskCatgeory = RiskCategory::all()->pluck('name', 'id')->toArray();

        foreach ($rows as $index => $row) {
            if ($row->filter()->isNotEmpty()) {
                if (isset($row['primary_id']) && isset($row['sub_id']) && isset($row['name']) && isset($row['type'])) {
                    $category_id = array_search(trim($row['type']), $riskCatgeory);

                    RisksTemplate::create([
                        'standard_id' => $standard,
                        'primary_id' => $row['primary_id'],
                        'sub_id' => $row['sub_id'],
                        'name' => trim($row['name']),
                        'category_id' => $category_id,
                        'risk_description' => trim($row['risk']),
                        'affected_properties' => preg_replace('/\s+/', '', $row['affected_propertyies']),
                        'treatment' => trim($row['treatment']),
                    ]);
                }
            }
        }
    }
}
