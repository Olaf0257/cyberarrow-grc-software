<?php

namespace App\Http\Controllers\RisksManagement\RiskRegister;

use App\Rules\ValidRiskRegisterName;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Utils\RegularFunctions;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\UserManagement\Admin;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\RiskManagement\RiskClose;
use App\Mail\RiskManagement\AssignRisk;
use App\Models\Compliance\ProjectControl;
use App\Models\RiskManagement\RiskCategory;
use App\Models\RiskManagement\RiskRegister;
use App\Models\UserManagement\AdminDepartment;
use App\Traits\RisksManagement\HelperMethodsTrait;
use App\Exports\RiskManagement\RiskRegister\RisksExport;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixScore;
use App\Rules\RiskManagement\ValidRiskAffectedProperties;
use App\Models\RiskManagement\RiskMappedComplianceControl;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixImpact;
use App\Models\RiskManagement\RiskMatrix\RiskScoreLevelType;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixLikelihood;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixAcceptableScore;
use Auth;

class RiskRegisterController
{
    use HelperMethodsTrait;

    private $baseViewPath = 'risk-management.risk-register.';

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $risksAffectedProperties['common'] = [
            'Confidentiality', 'Integrity', 'Availability',
        ];
        $risksAffectedProperties['other'] = [
            'Change Management', 'Ethical', 'Financial', 'Financial Reporting', 'Fraud', 'Geographic', 'IT Operations', 'Logical Access', 'Material Misstatement', 'Operational', 'Privacy', 'Regulatory / Compliance', 'Reputational', 'Strategy',
        ];
        $riskMatrixImpacts = RiskMatrixImpact::query()->select('name')->get()->pluck('name');
        $riskMatrixLikelihoods = RiskMatrixLikelihood::query()->select('name')->get()->pluck('name');
        $riskMatrixScores = RiskMatrixScore::query()->orderBy('likelihood_index', 'ASC')
            ->orderBy('impact_index', 'ASC')->select(['score', 'likelihood_index', 'impact_index'])->get()->split(count($riskMatrixLikelihoods));
        $riskScoreActiveLevelType = RiskScoreLevelType::where('is_active', 1)->with('levels')->first();

        return Inertia::render('risk-management/risk-register/RiskRegister', [
            'risksAffectedProperties' => $risksAffectedProperties,
            'riskMatrixImpacts' => $riskMatrixImpacts,
            'riskMatrixLikelihoods' => $riskMatrixLikelihoods,
            'riskMatrixScores' => $riskMatrixScores,
            'riskScoreActiveLevelType' => $riskScoreActiveLevelType
        ]);
    }

    public function riskDelete($id)
    {
        $risk = RiskRegister::findOrFail($id);
        $risk->delete();

        $data = [
            'status' => 200,
            'id' => $id,
            'message' => 'Risk deleted successfully.',
        ];

        Log::info('User has deleted a risk.', ['risk_id' => $id]);
        return response()->json($data);
    }

    public function riskCreate(Request $request)
    {
        $riskCategories = RiskCategory::get();
        $risk = new RiskRegister();
        $risksAffectedProperties['common'] = [
            'Confidentiality', 'Integrity', 'Availability',
        ];
        $risksAffectedProperties['other'] = [
            'Change Management', 'Ethical', 'Financial', 'Financial Reporting', 'Fraud', 'Geographic', 'IT Operations', 'Logical Access', 'Material Misstatement', 'Operational', 'Privacy', 'Regulatory / Compliance', 'Reputational', 'Strategy',
        ];

        /* Risk matrix likelihoods */
        $riskMatrixLikelihoods = RiskMatrixLikelihood::all();
        $riskMatrixImpacts = RiskMatrixImpact::all();
        $riskMatrixScores = RiskMatrixScore::orderBy('likelihood_index', 'ASC')
            ->orderBy('impact_index', 'ASC')->get()->split(count($riskMatrixLikelihoods));
        $riskScoreActiveLevelType = RiskScoreLevelType::where('is_active', 1)->with('levels')->first();

        $data['risk'] = $risk;
        $data['riskCategories'] = $riskCategories;
        $data['risksAffectedProperties'] = $risksAffectedProperties;
        $data['riskMatrixLikelihoods'] = $riskMatrixLikelihoods;
        $data['riskMatrixImpacts'] = $riskMatrixImpacts;
        $data['riskMatrixScores'] = $riskMatrixScores;
        $data['riskScoreActiveLevelType'] = $riskScoreActiveLevelType;

        Log::info('User is attempting to create a risk.');
        return Inertia::render('risk-management/risk-register/components/RiskRegisterCreate', ['data' => $data]);
    }

    public function riskStore(Request $request)
    {
        $request->validate([
            'risk_name' => [
                'required',
                'max:191',
                new ValidRiskRegisterName()
            ],
            'risk_description' => 'required',
            'treatment' => 'required',
            'category' => 'required',
            'affected_properties' => [
                'required',
                'max:191',
                new ValidRiskAffectedProperties(),
            ],

            'treatment_options' => 'required|in:Mitigate,Accept',
            'likelihood' => 'required',
            'impact' => 'required',
            'affected_functions_or_assets' => 'max:255'
        ]);

        $requestInputs = $request->toArray();

        $affectedProperties = implode(',', $requestInputs['affected_properties']);
        $riskScore = RiskMatrixScore::where('likelihood_index', $requestInputs['likelihood'])->where('impact_index', $requestInputs['impact'])->first();

        $riskScore = RiskMatrixScore::where('likelihood_index', $requestInputs['likelihood'])->where('impact_index', $requestInputs['impact'])->first();
        $inputs = [
            'category_id' => $requestInputs['category'],
            'name' => $requestInputs['risk_name'],
            'risk_description' => $requestInputs['risk_description'],
            'affected_properties' => $affectedProperties,
            'treatment' => $requestInputs['treatment'],
            'treatment_options' => $requestInputs['treatment_options'],
            'likelihood' => intval($requestInputs['likelihood']) + 1,
            'impact' => intval($requestInputs['impact']) + 1,
            'inherent_score' => $riskScore->score,
            'residual_score' => $riskScore->score,
            'data_scope' => $requestInputs['data_scope']
        ];

        if ($requestInputs['affected_functions_or_assets']) {
            $inputs['affected_functions_or_assets'] = $requestInputs['affected_functions_or_assets'];
        }

        $risk = RiskRegister::create($inputs);
        $risk->inherent_score = $riskScore->score;
        $risk->residual_score = $riskScore->score;
        $risk->status = $requestInputs['treatment_options'] === "Accept" ? "Close" : "Open";
        $risk->update();
        Log::info('User has created a risk.', ['risk_id' => $risk->id]);

        return redirect()->route('risks.register.index')->withSuccess('Risk added successfully!');
    }

    public function manualAssign(Request $request, $id)
    {
        $request->validate([
            'owner' => 'required|different:custodian',
            'custodian' => 'required|different:owner',
        ]);

        $risk = RiskRegister::with('mappedControls')->find($id);
        $riskInputs['owner_id'] = intval($request->owner);
        $riskInputs['custodian_id'] = intval($request->custodian);
        $before_update_custodian_id = $risk->custodian_id ?: null;
        $before_update_owner_id = $risk->owner_id ?: null;

        // if the custodian was not changed, do not send a new email.
        $send_new_custodian_email = $before_update_custodian_id != $riskInputs['custodian_id'];
        $send_new_owner_email = $before_update_owner_id != $riskInputs['owner_id'];

        $risk->update($riskInputs);
        $mappedControls = $risk->mappedControls ? $risk->mappedControls->first() : null;

        // Send email for new owner
        if ($send_new_owner_email) {
            $owner = Admin::find($riskInputs['owner_id']);
            $subject = "Assignment as risk owner";
            $data = [
                'greeting' => "Hello " . decodeHTMLEntity($owner->full_name),
                'title' => "You have been assigned as a risk owner for the below risk.",
                'risk' => $risk,
                'mappedControls' => $mappedControls
            ];
            Mail::to($owner->email)->send(new AssignRisk($data, $subject));
        }

        // Send email for previous owner, if he was replaced.
        if ($before_update_owner_id && $send_new_owner_email) {
            $removed_owner = Admin::find($before_update_owner_id);
            $subject = "Removal of ownership";
            $data = [
                'greeting' => "Hello " . decodeHTMLEntity($removed_owner->full_name),
                'title' => "You have been replaced as a risk owner for the below risk.",
                'risk' => $risk,
                'mappedControls' => $mappedControls,
            ];
            Mail::to($removed_owner->email)->send(new AssignRisk($data, $subject));
        }

        // Send email to custodian
        if ($send_new_custodian_email) {
            $custodian = Admin::find($riskInputs['custodian_id']);
            $subject = "Assignment as risk custodian";
            $data = [
                'greeting' => "Hello " . decodeHTMLEntity($custodian->full_name),
                'title' => "You have been assigned as a risk custodian for the below risk.",
                'risk' => $risk,
                'mappedControls' => $mappedControls,
            ];
            Mail::to($custodian->email)->send(new AssignRisk($data, $subject));
        }

        // Send email for previous custodian, if he was replaced.
        if ($before_update_custodian_id && $send_new_custodian_email) {
            $removed_custodian = Admin::find($before_update_custodian_id);
            $subject = "Removal of custodianship";
            $data = [
                'greeting' => "Hello " . decodeHTMLEntity($removed_custodian->full_name),
                'title' => "You have been replaced as a risk custodian for the below risk.",
                'risk' => $risk,
                'mappedControls' => $mappedControls
            ];
            Mail::to($removed_custodian->email)->send(new AssignRisk($data, $subject));
        }

        Log::info('User has manually assigned owner and custodian to a risk.', ['risk_id' => $id]);
        return redirect()->back()->withSuccess('Saved owner and custodian to risk');
    }

    public function riskShow($id)
    {
        $risk = RiskRegister::findOrFail($id);
        return Inertia::render('risk-management/risk-register/components/RiskRegisterShow', compact('id', 'risk'));
    }

    public function getContributorsList()
    {
        $dataScope = request()->has('data_scope') ? request()->input('data_scope') : '1-0';
        $dataScope = explode('-', $dataScope);
        $departmentId = intval($dataScope[1]);

        $contributors = [];

        if ($departmentId === 0) {
            // we're in an organization
            $contributors = Admin::query()
                ->where('status', 'active')
                ->select(['first_name', 'last_name', 'id'])
                ->get();
        } else {
            $departments = RegularFunctions::getChildDepartments($departmentId);
            $admins = AdminDepartment::whereIn('department_id', $departments)->pluck('admin_id');
            $contributors = Admin::where('status', 'active')
                ->whereIn('id', $admins)
                ->select(['first_name', 'last_name', 'id'])
                ->get();
        }

        foreach ($contributors as $contributor) {
            if ($contributor->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Contributor', 'Risk Administrator'])) {
                $key = ucwords($contributor->first_name . ' ' . $contributor->last_name);
                $contributorArray[$key] = $contributor->id;
            }
        }
        return response()->json($contributorArray);
    }

    public function riskUpdate(Request $request, $Id)
    {
        $inputValidationRules = [
            'affected_properties' => [
                'required',
                'max:191',
                new ValidRiskAffectedProperties(),
            ],
            'treatment_options' => 'required|in:Mitigate,Accept',
            'likelihood' => 'required',
            'impact' => 'required',
        ];

        $riskInputs = [];
        // $input = collect($request->all())->map(function ($item, $key) {
        //     if ($key != 'affected_properties') {
        //         return RegularFunctions::cleanXSS($item);
        //     }

        //     return $item;
        // });

        $input = $request->toArray();

        if ($request->has('risk_name')) {
            $riskInputs['name'] = $input['risk_name'];

            // validation rule
//            $inputValidationRules['risk_name'] = "required|max:191|unique:risks_register,name,$Id,id,deleted_at,NULL";
            $inputValidationRules['risk_name'] = [
                'required',
                'max:191',
                new ValidRiskRegisterName($Id)
            ];
        }

        if ($request->has('risk_description')) {
            $riskInputs['risk_description'] = $input['risk_description'];

            // validation rule
            $inputValidationRules['risk_description'] = 'required';
        }

        if ($request->has('treatment')) {
            $riskInputs['treatment'] = $input['treatment'];

            // validation rule
            $inputValidationRules['treatment'] = 'required';
        }

        if ($request->has('category')) {
            $riskInputs['category_id'] = $input['category'];

            // validation rule
            $inputValidationRules['category'] = 'required';
        }

        if ($request->has('affected_functions_or_assets')) {
            $riskInputs['affected_functions_or_assets'] = $input['affected_functions_or_assets'];

            // validation rule
            $inputValidationRules['affected_functions_or_assets'] = 'required|max:255';
        }

        if ($request->treatment_options) {
            $riskInputs['treatment_options'] = $input['treatment_options'];
            $riskInputs['status'] = $input['treatment_options'] === "Accept" ? "Close" : "Open";
        }
        $request->validate($inputValidationRules);

        $risk = RiskRegister::find($Id);

        $affectedProperties = implode(',', $input['affected_properties']);

        $riskScore = RiskMatrixScore::where('likelihood_index', $input['likelihood'])->where('impact_index', $input['impact'])->first();

        $riskInputs = array_merge($riskInputs, [
            'affected_properties' => $affectedProperties,
            'treatment_options' => $input['treatment_options'],
            'likelihood' => $input['likelihood'] + 1,
            'impact' => $input['impact'] + 1,
            'inherent_score' => $riskScore->score,
            'residual_score' => $riskScore->score,
            'is_complete' => 1,
        ]);

        $risk->update($riskInputs);
        Log::info('User has updated a risk.', ['risk_id' => $Id]);
        return redirect()->back()->withSuccess('Risk updated successfully!');
    }

    public function riskEdit($id)
    {
        $riskCategories = RiskCategory::get();
        $risk = new RiskRegister();
        $risksAffectedProperties['common'] = [
            'Confidentiality', 'Integrity', 'Availability',
        ];
        $risksAffectedProperties['other'] = [
            'Change Management', 'Ethical', 'Financial', 'Financial Reporting', 'Fraud', 'Geographic', 'IT Operations', 'Logical Access', 'Material Misstatement', 'Operational', 'Privacy', 'Regulatory / Compliance', 'Reputational', 'Strategy',
        ];

        /* Risk matrix likelihoods */
        $riskMatrixLikelihoods = RiskMatrixLikelihood::all();
        $riskMatrixImpacts = RiskMatrixImpact::all();
        $riskMatrixScores = RiskMatrixScore::orderBy('likelihood_index', 'ASC')
            ->orderBy('impact_index', 'ASC')->get()->split(count($riskMatrixLikelihoods));
        $riskScoreActiveLevelType = RiskScoreLevelType::where('is_active', 1)->with('levels')->first();

        $data['risk'] = $risk;
        $data['riskCategories'] = $riskCategories;
        $data['risksAffectedProperties'] = $risksAffectedProperties;
        $data['riskMatrixLikelihoods'] = $riskMatrixLikelihoods;
        $data['riskMatrixImpacts'] = $riskMatrixImpacts;
        $data['riskMatrixScores'] = $riskMatrixScores;
        $data['riskScoreActiveLevelType'] = $riskScoreActiveLevelType;

        $edit_risk = RiskRegister::with('category')->findOrFail($id);
        Log::info('User is attempting to update a risk.', ['risk_id' => $id]);
        return Inertia::render('risk-management/risk-register/components/RiskRegisterCreate', ['data' => $data, 'id' => $id, 'risk' => $edit_risk]);

    }

    /* RETURN THE LIST COMPLIANCE CONTROLS TO BE MAPPED */
    public function getRiskMappingComplianceProjectControls(Request $request, $riskId)
    {
        $page = $request->page ?? 1;
        $per_page = $request->per_page ?? 10;
        $keyword = $request->search ?? null;

        $render = [];

        $complianceProjectControlsQuery = ProjectControl::query()->where('applicable', 1)
            ->with('project');

        if($keyword){
            $complianceProjectControlsQuery->where('name', 'LIKE', "%{$keyword}%")
                                            ->orWhere(DB::raw("CONCAT_WS(id_separator, primary_id, sub_id)"), 'LIKE', "%{$keyword}%");
        }

        if(isset($request->project_filter)){
            $complianceProjectControlsQuery->where('project_id', $request->project_filter);
        }

        if(isset($request->standard_filter)){
            $complianceProjectControlsQuery->whereHas('project', function ($q) use ($request) {
                $q->where('standard_id', $request->standard_filter);
            });
        }

        $complianceProjectControls = $complianceProjectControlsQuery->skip(--$page * $per_page)->take($per_page)->paginate($per_page);

        foreach ($complianceProjectControls as $complianceProjectControl) {
            $projectName = $complianceProjectControl->project ? $complianceProjectControl->project->name : '';
            $standardId = $complianceProjectControl->project ? $complianceProjectControl->project->standard_id : '';
            $standardName = $complianceProjectControl->project ? $complianceProjectControl->project->standard : '';

            $mapped = RiskMappedComplianceControl::where('risk_id', $riskId)->where('control_id', $complianceProjectControl->id)->exists();

            if ($complianceProjectControl->status == 'Not Implemented') {
                $status = "<span class='badge task-status-red w-100'>" . $complianceProjectControl->status . '</span>';
            } elseif ($complianceProjectControl->status == 'Implemented') {
                $status = "<span class='badge task-status-green w-100'>" . $complianceProjectControl->status . '</span>';
            } elseif ($complianceProjectControl->status == 'Rejected') {
                $status = "<span class='badge task-status-orange w-100'>" . $complianceProjectControl->status . '</span>';
            } else {
                $status = "<span class='badge task-status-blue w-100'>" . $complianceProjectControl->status . '</span>';
            }

            $isMapped = $mapped ? 'checked' : '';

            $render[] = [
                'project_name' => $projectName,
                'standard' => $standardName,
                'control_id' => $complianceProjectControl->controlId,
                'control_name' => $complianceProjectControl->name,
                'desc' => $complianceProjectControl->description,
                'status' => $status,
                'id' => $complianceProjectControl->id,
                'is_mapped' => $isMapped == "checked" ? true : false
            ];
        }
        $complianceProjectControls->setCollection(collect($render));
        $data = [
            'data' => $complianceProjectControls,
        ];

        return response()->json($data);
    }

    public function getMappedRiskComplianceControls(Request $request, $riskId)
    {
        $render = [];

        $mappedControls = RiskMappedComplianceControl::where('risk_id', $riskId)->with([
            'complianceProjectControl',
            'complianceProjectControl.responsibleUser',
            'complianceProjectControl.approverUser',
            'complianceProjectControl.project',
        ])->paginate($request->per_page ?? 10);

        foreach ($mappedControls as $mappedControl) {
            $mappedControlId = $mappedControl->complianceProjectControl ? ($mappedControl->complianceProjectControl ? $mappedControl->complianceProjectControl->controlId : '') : '';
            $responsible = $mappedControl->complianceProjectControl ? ($mappedControl->complianceProjectControl->responsibleUser ? $mappedControl->complianceProjectControl->responsibleUser->FullName : '') : '';
            $approver = $mappedControl->complianceProjectControl ? ($mappedControl->complianceProjectControl->approverUser ? $mappedControl->complianceProjectControl->approverUser->FullName : '') : '';
            $projectName = $mappedControl->complianceProjectControl ? ($mappedControl->complianceProjectControl->project ? $mappedControl->complianceProjectControl->project->name : '') : '';

            $render[] = [
                $mappedControlId,
                $projectName,
                $mappedControl->complianceProjectControl->name,
                $mappedControl->complianceProjectControl->description,
                $mappedControl->complianceProjectControl->frequency,
                $responsible,
                $approver,
            ];
        }
        $mappedControls->setCollection(collect($render));

        $data = [
            'data' => $mappedControls,
        ];

        return response()->json($data);
    }

    public function mapRiskControls(Request $request)
    {
        $request->validate([
            'risk_id' => 'required',
            'control_id' => 'required',
        ]);
        //mapping to new control
        $complianceControl = ProjectControl::find($request->control_id);

        /* RETURNING WHEN MODLE NOT FOUND*/
        if (!$complianceControl) {
            // compliance project control not found
            return response()->json([
                'success' => false,
            ]);
        }

        // Getting risk from register
        $risk = RiskRegister::with('owner', 'custodian')->find($request->risk_id);

        $previouslyMapped = RiskMappedComplianceControl::where('risk_id', $request->risk_id)->where('control_id', $request->control_id)->first();

        /* UN-MAPPING THE TARGET CONTROL WHEN ALREADY MAPPED*/
        /* when unmapping the mapped control by switting off the mapp switch*/
        if ($previouslyMapped) {
            \DB::beginTransaction();

            $previouslyMapped->delete();

            /* Reopening the risk*/
            $risk->status = 'Open';
            $risk->residual_score = $risk->inherent_score;
            $risk->update();

            \DB::commit();

            return response()->json([
                'success' => true,
            ]);
        }

        /* Mapping a new control*/
        try {
            //code...

            \DB::beginTransaction();
            /* DELETING THE PREVIOUSLY MAPPED CONTROL*/
            RiskMappedComplianceControl::where('risk_id', $request->risk_id)->delete();

            $mapped = RiskMappedComplianceControl::create([
                'risk_id' => $request->risk_id,
                'control_id' => $request->control_id,
            ]);

            // When mapped control has Implemented status then setting the residual score to acceptable score
            $implementComplianceControl = ProjectControl::where('id', $mapped->control_id)->where('status', 'Implemented')->first();

            if ($implementComplianceControl) {
                RegularFunctions::notifyRiskContributorList($implementComplianceControl, $risk);
            } else {
                $risk->status = 'Open';
                $risk->residual_score = $risk->inherent_score;
                $risk->update();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function riskExport(Request $request)
    {
        $fileName = 'Risk Register ' . date('d-m-Y') . '.xlsx';
        Log::info('User has downloaded a risks register report.');

        return Excel::download(new RisksExport(), $fileName);
    }
}
