<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class ControlTemplate implements WithHeadings {
    
    public function headings(): array {
        $headingArray = ['primary_id', 'sub_id', 'Name', 'Description'];

        return $headingArray;
    }

}
