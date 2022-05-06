<?php

namespace App\Imports\Compliance;

use App\Models\Compliance\StandardControl;
use App\Rules\Compliance\StandardControlUniqueId;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Facades\Log;

class ControlsImport implements ToCollection, WithHeadingRow, WithEvents
{/*
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    use Importable;
    use RegistersEventListeners;

    private $standard;
    private $idSeparator;
    public $msgBag;
    private $seed;

    public function __construct($standard = null, $idSeparator = null, $seed = null)
    {
        $this->standard = $standard;
        $this->idSeparator = $idSeparator;
        $this->seed = $seed;
    }

    public function collection(Collection $rows)
    {
        $standard = $this->standard;
        $idSeparator = !is_null($this->idSeparator) ? $this->idSeparator : '';
        $this->msgBag = new MessageBag();

        $rules = [
            '*.primary_id' => [
                'required',
                'max:191',
            ],
            '*.sub_id' => [
                'required',
                'max:191',
                'checkUnique'
            ],
            '*.name' => [
                'required',
                'string',
                'max:191',
                'distinct',
                Rule::unique('compliance_standard_controls')->where(function ($query) use ($standard) {
                    return $query->where('standard_id', $standard->id);
                }),
            ],
            '*.description' => [
                'required',
                'string',
                'max:50000'
            ],
            '*.required_evidence' => [
                'string',
                'max:50000'
            ]
        ];

        $messages = [
            "*.primary_id.required" => "one of the required data is missing.",
            "*.primary_id.max" => "the Primary ID must not be greater than 191 characters.",
            "*.sub_id.required" => "one of the required data is missing.",
            "*.sub_id.max" => "the Sub ID must not be greater than 191 characters.",
            '*.sub_id.checkUnique' => "A control with this Sub ID already exists for this Primary ID.",
            "*.description.required" => "one of the required data is missing.",
            "*.description.max" => "the description must not be greater than 50,000 characters.",
            "*.name.required" => "one of the required data is missing.",
            "*.name.max" => "the name must not be greater than 191 characters.",
            "*.name.unique" => "the name is already used for another control.",
            "*.name.distinct" => "the name has a duplicate value",
            "check_unique" => "a control with this Sub ID already exists for this Primary ID."
        ];

        if (!$this->seed) {
            Validator::extend('checkUnique', function ($field, $value, $parameters, $validator) {
                $row = explode('.', $field);
                $row = $row[0];
                $rows = $validator->getData();
                $rowsData = $rows[$row];

                //rule to check if primary and sub id are same in the csv data
                $duplicate_ids = array_filter($rows, function($key) use ($rowsData, $value) {
                    return $key['primary_id'] === $rowsData['primary_id'] && $key['sub_id'] === $value;
                });

                if (count($duplicate_ids) > 1) {
                    return false;
                }

                // rule to check if primary and sub id are same in the database
                $id_exists = StandardControl::where('standard_id', $this->standard->id)
                    ->where('primary_id', $rowsData['primary_id'])
                    ->where('sub_id', $value)
                    ->exists();

                if ($id_exists) {
                    return false;
                }
                return true;
            });
            $validator = Validator::make($rows->toArray(), $rules, $messages);

            $errors = array();
            foreach ($validator->errors()->messages() as $key => $error) {
                $attrLine = explode(".", $key);
                $attrLine = $attrLine[0] + 2;
                $errMessage = "On line {$attrLine} - $error[0]";
                $errors[] = $errMessage;
            }

            if ($validator->fails()) {
                Log::error([
                    "standard" => $standard->name,
                    'validator failed' => $validator->errors()
                ]);

                return $this->msgBag->add('csv_upload_errors', $errors);
            }
        }

        $controlsAdded = 0;
        foreach ($rows as $key => $row) {
            $index = $key + 1;
            if ($row->filter()->isNotEmpty()) {
                if (isset($row['primary_id']) && isset($row['sub_id']) && isset($row['name']) && isset($row['description'])) {
                    try {
                        StandardControl::create([
                            'standard_id' => $standard->id,
                            'primary_id' => $row['primary_id'],
                            'sub_id' => $row['sub_id'],
                            'id_separator' => $idSeparator,
                            'name' => $row['name'],
                            'slug' => Str::slug($row['name']),
                            'description' => $row['description'],
                            'required_evidence' => $row['required_evidence']??null,
                        ]);
                        $controlsAdded++;
                    } catch (QueryException $exception) {
                        $this->msgBag->add('csv_upload_errors', "On line $index - could not save data");
                        continue;
                    }
                } else {
                    $this->msgBag->add('csv_upload_errors', "On line $index - could not find all required data.");
                    return redirect()->back();
                }
            }
        }
        Log::info([
            "Seeding standard controls",
            "standard" => $standard->name,
            "csv rows" => $rows->count(),
            "controls added to db" => $controlsAdded
        ]);
    }

    /**
     * @param BeforeImport $event
     * @return void
     * base method
     */
    public static function beforeImport(BeforeImport $event)
    {
    }

    public function rules(): array
    {
        return [
            'name' => Rule::unique('controls', 'name'),
        ];
    }
}
