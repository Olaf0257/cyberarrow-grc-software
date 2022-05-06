<?php

namespace App\Imports\RiskManagement;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\RiskManagement\RiskCategory;

class RiskTemplateCategoriesImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if ($row->filter()->isNotEmpty()) {
                if (isset($row['name']) && isset($row['order_number'])) {
                    RiskCategory::create([
                        'name' => $row['name'],
                        'order_number' => $row['order_number']
                    ]);
                }
            }
        }
    }
}
