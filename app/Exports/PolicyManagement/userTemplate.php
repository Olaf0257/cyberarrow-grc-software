<?php

namespace App\Exports\PolicyManagement;

use Maatwebsite\Excel\Concerns\WithHeadings;

class userTemplate implements WithHeadings
{
    public function headings(): array {
        $headingArray = ['first_name', 'last_name', 'email'];

        return $headingArray;
    }
}
