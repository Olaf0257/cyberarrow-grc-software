<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class QuestionsTemplateExport implements WithHeadings{

    public function headings(): array
    {
        return [
            'question',
            'domain'
        ];
    }
}
