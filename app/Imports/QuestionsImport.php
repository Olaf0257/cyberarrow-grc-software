<?php

namespace App\Imports;

use App\Models\ThirdPartyRisk\Domain;
use App\Models\ThirdPartyRisk\Question;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithValidation, WithHeadingRow
{
    use Importable;
    private $questionnaire_id;

    public function __construct($questionnaire_id)
    {
        $this->questionnaire_id = $questionnaire_id;
    }

    public function model(array $row)
    {
        return new Question([
            'text' => $row['question'],
            'questionnaire_id' => $this->questionnaire_id,
            'domain_id' => Domain::query()->where('order_number', $row['domain'])->first()->id
        ]);
    }

    public function rules(): array
    {
        return [
            'question' => [
                'required',
                'max:825',
                'string',
                Rule::unique('third_party_questions', 'text')->where(function ($query) {
                    return $query->where('questionnaire_id', $this->questionnaire_id);
                })
            ],
            'domain' => 'required|exists:third_party_domains,order_number'
        ];
    }
}
