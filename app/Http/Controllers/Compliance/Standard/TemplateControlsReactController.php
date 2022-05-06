<?php

namespace App\Http\Controllers\Compliance\Standard;

use App\Exports\ControlTemplate;
use App\Http\Controllers\Controller;
use App\Imports\Compliance\ControlsImport;
use App\Rules\Compliance\StandardControlUniqueId;
use App\Utils\RegularFunctions;
use App\Models\Compliance\Standard;
use App\Models\Compliance\StandardControl;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class TemplateControlsReactController extends Controller
{
    protected $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            $this->loggedUser = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function view(Request $request, Standard $standard)
    {
        view()->share('pageTitle', decodeHTMLEntity($standard->name) . " associated controls");
        return inertia('compliance-template/ControlList', compact('standard'));
        // return view('compliance.templates.controls.view', compact('standard'));
    }

    public function create(Request $request, Standard $standard)
    {
        /* checking if it's a default standard */
        if ($standard->is_default) {
            return redirect()->back()->with('error', "This is a default standard and therefore additional control(s) can't be added.");
        }

        $control = new StandardControl();
        $control->standard = $standard;
        $idSeparatorsd = $control->idSeparators;

        //Formatting Data for React Select
        $data = [];
        $i = 0;
        foreach ($idSeparatorsd as $key => $idSeparator) {
            $data[$i]['label'] = $idSeparator;
            $data[$i]['value'] = $key;
            $i++;
        }
        $idSeparators = $data;

        return inertia('compliance-template/ControlCreate', compact('control', 'idSeparators', 'standard'));
        // return view('compliance.templates.controls.create', compact('control', 'idSeparators', 'standard'));
    }

    public function store(Request $request, Standard $standard)
    {
        /* checking if it's a default standard */

        if ($standard->is_default) {
            return back()->with('error', "This is a default standard and therefore additional control(s) can't be added.");
        }

        $request->request->add(['slug' => Str::slug($request->name)]);

        $this->validate($request, [
            'name' => [
                'required',
                'max:255',
                Rule::unique('compliance_standard_controls')->where(function ($query) use ($standard) {
                    return $query->where('standard_id', $standard->id);
                }),
            ],
            'description' => 'required|max:50000',
            'primary_id' => 'required|max:20',
            'sub_id' => [
                'required',
                'max:20',
                new StandardControlUniqueId($request->primary_id, $standard->id)
            ],
            'id_separator' => 'required',
        ], [
            'name.required' => 'The Name field is required',
            'description.required' => 'The Description field is required',
            'primary_id.required' => 'The Primary ID field is required',
            'sub_id.required' => 'The Sub ID field is required',
            'id_separator.required' => 'The ID Separator field is required',
        ]);

        $input = $request->toArray();

        $input['standard_id'] = $standard->id;
        $control = StandardControl::create($input);

        Log::info('User has created a new compliance template control', [
            'user_id' => auth()->id(),
            'control_id' => $control->id
        ]);

        return redirect(route('compliance-template-view-controls', [$standard->id]))->with('success', 'Control Created Succesfully!');
    }

    public function edit(Request $request, Standard $standard, StandardControl $control)
    {
        /* checking if it's a default standard */
        if ($standard->is_default) {
            return redirect()->back()->with('error', "This is a default control and therefore can't be edited.");
        }

        $control->standard = $standard;
        $idSeparatorsd = $control->idSeparators;

        Log::info('User is attempting to update a compliance template control', [
            'user_id' => auth()->id(),
            'control_id' => $control->id
        ]);

        //Formatting Data for React Select
        $data = [];
        $i = 0;
        foreach ($idSeparatorsd as $key => $idSeparator) {
            $data[$i]['label'] = $idSeparator;
            $data[$i]['value'] = $key;
            $i++;
        }
        $idSeparators = $data;

        return inertia('compliance-template/ControlCreate', compact('control', 'idSeparators', 'standard'));
        // return view('compliance.templates.controls.create', compact('control', 'idSeparators', 'standard'));
    }

    public function update(Request $request, Standard $standard, StandardControl $control)
    {
        /* checking if it's a default standard */
        if ($standard->is_default) {
            return redirect()->back()->with('error', "This is a default control and therefore can't be updated.");
        }

        $request->request->add(['slug' => Str::slug($request->name)]);
        $this->validate($request, [
            'name' => [
                'required',
                'max:255',
                Rule::unique('compliance_standard_controls')->where(function ($query) use ($standard) {
                    return $query->where('standard_id', $standard->id);
                })->ignore($control->id)
            ],
            'slug' => 'required',
            'description' => 'required',
            'primary_id' => 'required',
            'sub_id' => [
                'required',
                'max:20',
                new StandardControlUniqueId($request->primary_id, $standard->id, $control->id)
            ],
            'id_separator' => 'required',
        ], [
            'name.required' => 'The Name field is required',
            'description.required' => 'The Description field is required',
            'primary_id.required' => 'The Primary ID field is required',
            'sub_id.required' => 'The Sub ID field is required',
            'id_separator.required' => 'The ID Separator field is required',
        ]);

        $input = $request->all();
        $updated = $control->fill($input)->save();

        Log::info('User has updated a compliance template control', [
            'user_id' => auth()->id(),
            'control_id' => $control->id
        ]);

        return redirect(route('compliance-template-view-controls', [$standard->id]))->with('success', 'Control Updated Succesfully!');;
    }

    public function delete(Request $request, Standard $standard, StandardControl $control)
    {
        /* checking if it's a default standard */
        if ($standard->is_default) {
            return redirect()->back()->with('error', "This is a default control and therefore can't be deleted.");
        }

        $control_id = $control->id;
        $control->delete();
        Log::info('User has deleted a compliance template control', [
            'user_id' => auth()->id(),
            'control_id' => $control_id
        ]);

        return redirect(route('compliance-template-view-controls', [$standard->id]))->with('success', 'Control deleted successully.');
    }

    public function uploadCsv(Request $request, Standard $standard)
    {
        return view('compliance.templates.controls.upload-csv', compact('standard'));
    }

    public function downloadTemplate(Request $request, Standard $standard)
    {
        return Excel::download(new ControlTemplate(), 'control.csv');
    }

    public function uploadCsvStore(Request $request, Standard $standard)
    {
        /* checking if it's a default standard */
        if ($standard->is_default) {
            return back()->with('error', "This is a default standard and therefore additional control(s) can't be added.");
        }

        $request->validate([
            'csv_upload' => 'required|mimes:csv,txt',
            'id_separator' => 'nullable',
        ], [
            'csv_upload.required' => 'The CSV upload field is required',
            'csv_upload.mimes' => 'CSV format error',
        ]);

        $controlsCsvfile = $request->file('csv_upload');
        $file_data = file_get_contents($controlsCsvfile);

        /* When file encoding is not UTF-8.  Converting file encoding to utf-8 and rewriting the same file */
        if (!mb_check_encoding($file_data, 'UTF-8')) {
            $utf8_file_data = utf8_encode($file_data);

            file_put_contents($controlsCsvfile, $utf8_file_data);
        }

        $csvDatas = array_map('str_getcsv', file($controlsCsvfile));

        $csvIsEmpty = true;

        // checking fully empty csv file
        foreach ($csvDatas as $key => $csvData) {
            if (array_key_exists($key, $csvData)) {
                $csvIsEmpty = is_null($csvData[$key]);
            }
        }

        // showing validation error when file is fully empty
        if ($csvIsEmpty) {
            return back()->with('error', 'Csv file is empty');
        }

        // show error when Csv file row has not more than one row
        if (count($csvDatas) < 2) {
            return back()->with('error', 'Csv file is missing body content');
        }

        // checking required header
        $requiredHeaders = ['primary_id', 'sub_id', 'name', 'description'];
        $headerDiff = array_diff($requiredHeaders, array_map('strtolower', $csvDatas[0]));

        if (count($headerDiff) > 0) {
            return back()->with('error', 'Csv file do not have required headers');
        }
        Log::info('User is attempting to import via CSV new compliance template controls', [
            'user_id' => auth()->id(),
        ]);
        $import = new ControlsImport($standard, $request->id_separator);
        $importRes = Excel::import($import, $controlsCsvfile);

        $messages = $import->msgBag->getMessages();

        if (isset($messages['csv_upload_errors'])) {
            return back()->with('csv_upload_error', $messages['csv_upload_errors'][0]);
        }

        Log::info('User has imported new compliance template controls via CSV', [
            'user_id' => auth()->id(),
        ]);

        return redirect(route('compliance-template-view-controls', [$standard->id]))->with('success', 'Controls Uploaded Successfully!');;
    }

    public function getJsonData(Request $request, Standard $standard)
    {
        $page = $request->page ?? 1;
        $size = $request->per_page ?? 10;
        $sortingRequest = $request->order;
        $sortingColumn = 'id';
        $sortingOrder = 'desc';

        if ($sortingRequest) {
            // Getting sorting order
            $sortingOrder = $sortingRequest[0]['dir'];

            // Getting sorting column
            if ($sortingRequest[0]['column'] == 0) {
                // code...
            } elseif ($sortingRequest[0]['column'] == 1) {
                $sortingColumn = 'name';
            } elseif ($sortingRequest[0]['column'] == 2) {
                $sortingColumn = 'description';
            }
        }

        $start = $request->start;
        // $length = $request->length;
        $draw = $request->draw;
        $keyword = $request->search;
        $standardControls = $standard->controls->pluck('id');
        $count = $standard->controls->count();
        $controls = StandardControl::select(['id', 'name', 'description', 'created_at', 'primary_id', 'sub_id', 'id_separator'])
            ->whereIn('id', $standardControls)
            ->when($request->search != null, function ($query) use ($keyword) {
                return $query->where('name', 'LIKE', '%' . $keyword . '%');
            })
            ->offset($start)
            ->take($size)
            ->paginate($size);

        return response()->json([
            'data' => $controls,
            'total' => $count,
        ]);

        $render = [];

        foreach ($controls as $control) {
            $actions = "<div class='btn-group'>";

            if (!$standard->is_default) {
                $actions .= "<a href='" . route('compliance-template-edit-controls', [$standard->id, $control->id]) .
                    "' title='Edit Information' class='btn btn-info btn-xs waves-effect waves-light' data-toggle='tooltip' data-original-title='Edit'><i class='fe-edit'></i></a>
                            <a href='" . route('compliance-template-delete-controls', [$standard->id, $control->id]) . "' title='Delete' class='btn btn-danger btn-xs waves-effect waves-light'
                                data-animation='blur' data-plugin='custommodal' data-overlayColor='#38414a'><i class='fe-trash-2'></i></a>";
                $actions .= '</div>';
            }

            $render[] = [
                $control->controlId,
                $control->name,
                $control->description,
                $actions,
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => count($count),
            'recordsFiltered' => count($count),
            'data' => $render,
        ];

        echo json_encode($response);
    }
}
