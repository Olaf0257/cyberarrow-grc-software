<?php

namespace App\Imports\RiskManagement;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use App\Models\RiskManagement\RiskCategory;
use App\Models\RiskManagement\RiskRegister;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixScore;
use App\Rules\RiskManagement\ValidRiskAffectedProperties;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixImpact;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixLikelihood;

class RiskImport implements ToCollection, WithHeadingRow
{
    public $validAffectedProperties = [
        'Confidentiality',
        'Integrity',
        'Availability',
        'Change Management', 'Ethical', 'Financial', 'Financial Reporting', 'Fraud',
        'Geographic', 'IT Operations', 'Logical Access', 'Material Misstatement', 'Operational', 'Privacy',
         'Regulatory / Compliance', 'Reputational', 'Strategy',
    ];

    public function __construct($request)
    {
        $this->request = $request;
    }

    public $msgBag;

    public function collection(Collection $rows)
    {
        $this->msgBag = new MessageBag();
        $totalValidRows = 0;

        //getting csv file
        $csvDatas = array_map('str_getcsv', file($this->request->csv_upload));

        $csvIsEmpty = true;

        // checking fully empty csv file
        foreach ($csvDatas as $key => $csvData) {
            if (array_key_exists($key, $csvData)) {
                $csvIsEmpty = is_null($csvData[$key]);
            }
        }

        // showing validation error when file is fully empty
        if ($csvIsEmpty) {
            return $this->msgBag->add('csv_upload_error', 'Csv file is empty');
        }

        // checking required header
        $requiredHeaders = [
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
        $strlower = array_map('strtolower', $csvDatas[0]);

        //trim space for header
        $headerDiff = array_diff($requiredHeaders, array_map('trim', $strlower));

        if (count($headerDiff) > 0) {
            return $this->msgBag->add('csv_upload_error', 'CSV file does not have required headers.');
        }

        // show error when Csv file row has not more than one row
        if (count($csvDatas) < 2) {
            return $this->msgBag->add('csv_upload_error', 'CSV file is missing body content.');
        }

        //Getting all risk category
        // $riskCatgeory = RiskCategory::all();

        $invalidRows = [];
        $validRowsCount = 0;

        DB::beginTransaction();

        foreach ($rows as $key => $row) {
            if ($row->filter()->isEmpty()) {
                $this->msgBag->add('csv_upload_error', 'Csv file is empty');
                continue;
            }

            //triming values from csv file
            $row['name'] = trim($row['name']);
            $row['risk_description'] = trim($row['risk_description']);
            $row['affected_functions_or_assets'] = trim($row['affected_functions_or_assets']);
            $row['treatment_options'] = trim($row['treatment_options']);

            //replacing white space
            $exploded_values =  explode(',', $row['affected_properties']);
            $trimmed_affected_properties = [];
            foreach ($exploded_values as $value) {
                $value = trim($value);
                array_push($trimmed_affected_properties, $value);
            }
            $row['affected_properties'] = implode(',',$trimmed_affected_properties);

            if (!$this->validateRaw($row,$key)) {
                $invalidRows[] = $key + 1;
                DB::rollback();
                break;
            }
            ++$totalValidRows;

            $affectedProperties = array_intersect($this->validAffectedProperties, $trimmed_affected_properties);
            //checking if category exist
            $targetCategory = RiskCategory::where('order_number',$row['category'])->first();
            if (!$targetCategory) {
                $this->msgBag->add('csv_upload_error', 'Category not found.');
                continue;
            }
            $riskScore = RiskMatrixScore::where('likelihood_index', $row['likelihood']-1)->where('impact_index', $row['impact']-1)->first();
            ++$validRowsCount;

            $likelihoodId = $row['likelihood'] ? $row['likelihood'] - 1 : 1;
            $impactId = $row['impact'] ? $row['impact'] - 1 : 1;

            RiskRegister::create([
                            'category_id' => $targetCategory->id,
                            'name' => $row['name'],
                            'risk_description' => $row['risk_description'],
                            'affected_properties' => implode(',', $affectedProperties),
                            'affected_functions_or_assets' => $row['affected_functions_or_assets'],
                            'treatment' => $row['treatment'],
                            'category' => $row['category'],
                            'treatment_options' => $row['treatment_options'],
                            'likelihood' => $row['likelihood'],
                            'impact' => $row['impact'],
                            'inherent_score' => $riskScore->score,
                            'residual_score' => $riskScore->score,
                        ]);
        }
        DB::commit();

        if ($totalValidRows == 0) {
            $this->msgBag->add('csv_upload_error', 'CSV does not have valid rows');
        }

        if ($validRowsCount > 0 && $invalidRows > 0) {
            if (count($rows) == $validRowsCount) {
                // $this->msgBag->add('csv_upload_error', 'All rows successfully inserted');
            } else {
                $implodeRows = implode(', ', $invalidRows);
                $this->msgBag->add('csv_upload_error', $validRowsCount.' row(s) inserted but '.($implodeRows).'  row(s) can not be inserted due to invalid data(s)');
            }
        }
    }

    private function validateRaw($row,$key)
    {
        $isValidRow = false;
        $likelihoodIndexes = RiskMatrixLikelihood::get()->map(function ($item, $key) {
            return $item->index +1;
        })->implode(',');
        $impactIndexes = RiskMatrixImpact::get()->map(function ($item, $key) {
            return $item->index +1;
        })->implode(',');

        $validator = Validator::make($row->toArray(), [
            'name' => [
                'required',
                'max:191',
                Rule::unique('risks_register')->where(function ($query) {
                    $query->where('deleted_at', null);
                }),
            ],
            'risk_description' => 'required',
            'affected_properties' => ['required', new ValidRiskAffectedProperties()],
            'affected_functions_or_assets' => 'required|max:255',
            'treatment' => 'required',
            'category' => 'required|integer|between:1,13',
            'treatment_options' => 'required|in:Mitigate,Accept',
            'likelihood' => 'required|in:'.$likelihoodIndexes,
            'impact' => 'required|in:'.$impactIndexes,
        ]);

        if ($validator->fails()) {
            $row_num=$key+2;
            $this->msgBag->add('csv_upload_error','In line ' . $row_num . ', ' . implode("",$validator->errors()->all()));
            $isValidRow = false;
        }
        else{
            $isValidRow = true;
        }
        return $isValidRow;
    }
}
