<?php

namespace App\Http\Controllers\RisksManagement\RiskSetup\Wizard;

use App\Http\Controllers\Controller;
use App\Models\Compliance\Project;
use App\Models\Compliance\ProjectControl;
use App\Models\RiskManagement\RiskCategory;
use App\Models\RiskManagement\RiskMappedComplianceControl;
use App\Models\RiskManagement\RiskRegister;
use App\Models\RiskManagement\RiskStandard;
use App\Models\RiskManagement\RisksTemplate;
use App\Models\DataScope\Scopable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixScore;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixImpact;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixLikelihood;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixAcceptableScore;

class RiskSetupController extends Controller
{
    public function index()
    {
        // return view('risk-management.risk-setup.wizard.index');
        // return view('app');
        return Inertia::render('risk-management/risk-setup/wizard/RiskSetupWizard');

    }

    public function automatedRiskSetup(Request $request)
    {
        \DB::raw(\DB::select('SET PERSIST information_schema_stats_expiry = 0'));
        \DB::raw(\DB::select('SET GLOBAL information_schema_stats_expiry=0'));
        
        $validator = Validator::make($request->all(), [
            'project' => 'required',
            'riskSetUpStandard' => 'required|in:ISO/IEC 27002:2013,ISR V2,SAMA Cyber Security Framework,NCA ECC-1:2018,NCA CSCC-1:2019,UAE IA',
            'data_scope' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('risk_setup_errors', 'Selected Stanadard is not valid');
        }
        $selectedStdId = RiskStandard::where('name', $request->riskSetUpStandard)->first()['id'];
        $complianceControls = ProjectControl::where(['project_id'=>$request->project,'applicable'=>1]);
        $matchedRisk = RisksTemplate::where('standard_id', $selectedStdId)->get();
        $riskAcceptableScore = RiskMatrixAcceptableScore::first();

        // /* Setting the default value, ref:RiskRegisterObserver */
        $likelihood = 2;
        $impact = 2;

        //Replacing default configuration and added middleImpact result
        $likelihoodCount = RiskMatrixLikelihood::count();
        $impactCount = RiskMatrixImpact::count();

        $is_3x3 = ($likelihoodCount === $impactCount) && ($likelihoodCount === 3);
        $likelihood = $is_3x3 ? 2 : intval(floor($likelihoodCount / 2));
        $impact = $is_3x3 ? 2 : intval(floor($impactCount / 2));

        $inherent_score = 0;
        $residual_score = 0;
        $riskScore = RiskMatrixScore::where('likelihood_index', $likelihood-1)->where('impact_index', $impact-1)->first('score');
        if ($riskScore) {
            $inherent_score = $riskScore->score;
            $residual_score = $riskScore->score;
        }

        $riskRegister = [];
        foreach ($matchedRisk as $key => $eachMatchedRisk) {
            $riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id] = $eachMatchedRisk->toArray();
            unset($riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['primary_id']);
            unset($riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['standard_id']);
            unset($riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['sub_id']);
            unset($riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['id']);
            $riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['created_at'] = date("Y-m-d H:i:s");
            $riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['updated_at'] = date("Y-m-d H:i:s");
            $riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['is_complete'] = 0;
            $riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['likelihood'] = $likelihood;
            $riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['impact'] = $impact;
            $riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['inherent_score'] = $inherent_score;
            $riskRegister[$eachMatchedRisk->primary_id."-".$eachMatchedRisk->sub_id]['residual_score'] = $residual_score;
            if($key == 0){
                $complianceControls->where(['project_id'=>$request->project,'primary_id'=>$eachMatchedRisk->primary_id,'sub_id'=>$eachMatchedRisk->sub_id]);
            }else{
                $complianceControls->orWhere('primary_id',$eachMatchedRisk->primary_id)->where('sub_id',$eachMatchedRisk->sub_id)->where('project_id',$request->project);
            }
        }

        

        $riskRegisterFilterd = [];
        $mappingControls = [];
        $riskRegisterAutoIncrement = \DB::select("show table status like 'risks_register'");
        $firstRiskRegisterId = $riskRegisterAutoIncrement[0]->Auto_increment;

        $riksMappedComplianceControl = \DB::select("show table status like 'risks_mapped_compliance_controls'");
        $firstRiskMappedComplianceControlId = $riksMappedComplianceControl[0]->Auto_increment;

        // Work for app-data scoping on bulk insertion
        $organizationId = $departmentId = 0;
        if($request->data_scope){
            $dataScope = explode('-', $request->data_scope);
            $organizationId = $dataScope[0];
            $departmentId = $dataScope[1];
        }
        $riskRegisterScopable = $complianceControlScopable = [];
        $countMatchedComplianceControl = 0;

        foreach ($complianceControls->get() as $key => $matchedComplianceControl) {
            $countMatchedComplianceControl++;
            $i = 0;
            $i = $firstRiskRegisterId;
            $mappingControls[] = [
                'risk_id' => $i,
                'control_id' => $matchedComplianceControl->id,
            ];

            //Scopeable data prepare
            if($departmentId > 0){
                $riskRegisterScopable[] = [
                    'organization_id' => $organizationId,
                    'department_id'=> $departmentId,
                    'scopable_id'=>$i,
                    'scopable_type'=>'App\Models\RiskManagement\RiskRegister'
                ];

                $complianceControlScopable[] = [
                    'organization_id' => $organizationId,
                    'department_id'=> $departmentId,
                    'scopable_id'=> $firstRiskMappedComplianceControlId,
                    'scopable_type'=>'App\Models\RiskManagement\RiskMappedComplianceControl'
                ];
                
            }else{
                $riskRegisterScopable[] = [
                    'organization_id' => $organizationId,
                    'department_id'=> NULL,
                    'scopable_id'=>$i,
                    'scopable_type'=>'App\Models\RiskManagement\RiskRegister'
                ];

                $complianceControlScopable[] = [
                    'organization_id' => $organizationId,
                    'department_id'=> $departmentId,
                    'scopable_id'=> $firstRiskMappedComplianceControlId,
                    'scopable_type'=>'App\Models\RiskManagement\RiskMappedComplianceControl'
                ];
            }

            $firstRiskMappedComplianceControlId++;
            $firstRiskRegisterId++;
            $riskToImport = $riskRegister[$matchedComplianceControl->primary_id.'-'.$matchedComplianceControl->sub_id];
            
            /* Making the risk close when mapped control is implemented */
            if ($matchedComplianceControl->status == "Implemented" ) {
                $riskToImport['status'] = 'Close';
                $riskToImport['residual_score'] = $riskAcceptableScore->score;
            } else {
                $riskToImport['status'] = 'Open';
            }

            $riskRegisterFilterd[] = $riskToImport;
        }

        if(count($riskRegisterFilterd) == $countMatchedComplianceControl){
            $this->resetRiskSetup(); 
            \DB::beginTransaction();
            $riskInsertStatus = RiskRegister::insert($riskRegisterFilterd);
            $mappingControlsInsertStatus = RiskMappedComplianceControl::insert($mappingControls);
            
            //Data Scopable Insertion
            $riskRegisterScope = Scopable::insert($riskRegisterScopable);
            $complianceControlScope = Scopable::insert($complianceControlScopable);

            if($riskInsertStatus && $mappingControlsInsertStatus && $riskRegisterScope && $complianceControlScope){
                //commit db
                \DB::commit();
                return redirect()->route('risks.register.index')->withSuccess('Risks successfully mapped with selected project using automated method.');
            }else{
                //rollback db
                \DB::rollback();
                return redirect()->back()->withErrors('Unable to add the risks.');
            }
        }else{
            return redirect()->back()->withErrors('Risk does not mapped. Please try again.');
        }
    }


    public function getProjectsByStandard(Request $request)
    {
        $request->validate([
            'standard' => [
                    'required',
                    Rule::in(['ISO/IEC 27002:2013', 'ISR V2', 'SAMA Cyber Security Framework', 'NCA ECC-1:2018', 'NCA CSCC-1:2019','UAE IA']),
                ],
        ]);

        $projects = $this->getProjectsByStandardQuery($request->standard)->get();

        return response()->json($projects);
    }

    public function checkComplianceProjectsExists(Request $request)
    {
        // dd($request->all);
        $request->validate([
            'standard' => [
                'required',
                Rule::in(['ISO/IEC 27002:2013', 'ISR V2', 'SAMA Cyber Security Framework', 'NCA ECC-1:2018', 'NCA CSCC-1:2019','UAE IA']),
            ],
        ]);

        $projectsCount = $this->getProjectsByStandardQuery($request->standard)->count();

        if ($projectsCount != 0) {
            return response()->json([
                'success' => true,
                'exists' => true,
            ]);
        } else {
            return response()->json([
                'success' => true,
                'exists' => false,
            ],200);
        }
    }

    public function getRiskImportSetupPage(Request $request)
    {
        $riskSetUpStandard = $request->standard;
        $projects = $this->getProjectsByStandardQuery($request->standard)->get();
        if ($request->setupApproach == 'Automated') {
            return response()->json([
                'success' => true,
                'type' => 'automated',
                'projects' => $projects,
                'riskSetUpStandard' => $riskSetUpStandard
            ]);
        } else {
            $riskCategories = RiskCategory::whereHas('risks.standard', function ($query) use ($request) {
                $query->where('name', $request->standard);
            })->get();

            return response()->json([
                'success' => true,
                'type' => 'yourself',
                'projects' => $projects,
                'riskCategories' => $riskCategories,
                'categories_count' => count($riskCategories),
            ]);
        }
    }

    /*
    ** Matching risk standard with compliance standard
    */
    protected function getProjectsByStandardQuery($standardName)
    {
        switch ($standardName) {
            case 'ISO/IEC 27002:2013':
                /*Compliace stan*/
                $standardName = 'ISO/IEC 27001-2:2013';
                break;

            default:
                // code...
                break;
        }

        return Project::where('standard', $standardName);
    }

    public function getRiskImportRisksListSection(Request $request)
    {
        $riskCategory = 'Confirm';
        $isConfirmTab = $request->is_confirm_tab == 'false' ? false : true;
        $selectedRiskIds = is_null($request->selected_risk_ids) ? [] : $request->selected_risk_ids;
        $currentTabIndex = $request->current_tab_index;


    
        $projects = $this->getProjectsByStandardQuery($request->standard)->get();

        if ($request->is_confirm_tab == 'false') {
            $riskCategory = RiskCategory::find($request->category_id);
            if (!$riskCategory) {
                return response()->json([
                    'success' => false,
                ]);
            }
        }


        // var_dump($selectedRiskIds);
        // dd($isConfirmTab);


        $risks = RisksTemplate::whereHas('standard', function ($q) use ($request) {
            $q->where('name', $request->standard);
        })
                ->where(function ($q) use ($request, $isConfirmTab, $selectedRiskIds) {
                    if (!$isConfirmTab) {
                        $q->where('category_id', $request->category_id);
                    } else {
                        $q->whereIn('id', $selectedRiskIds);
                    }

                    if ($request->has('risk_name_search_query') && !is_null($request->risk_name_search_query)) {
                        $q->where('name', 'like', '%'.$request->risk_name_search_query.'%');
                    }
                })
                ->with('category')
                    ->paginate(20);

        return response()->json([
            'success' => true,
            'risks' => $risks,
            'risksCount' => count($risks),
            'currentTabIndex' => $currentTabIndex,
            'isConfirmTab' => $isConfirmTab,
            'riskCategory' => $riskCategory,
            'selectedRiskIds' => $selectedRiskIds,
            'projects' => $projects,
            'category' => $isConfirmTab ? 'Confirm' : $riskCategory->name,
        ]);
    }

    public function yourselfRisksSetup(Request $request)
    {
        $selectedRiskIds = is_null($request->selected_risk_ids) ? [] : $request->selected_risk_ids;
        if (!$selectedRiskIds) {
            return redirect()->back()->withErrors('And any risk selected');
        }
        $riskAcceptableScore = RiskMatrixAcceptableScore::first();
        $risks = RisksTemplate::whereIn('id', $selectedRiskIds)
            ->get([
                'primary_id',
                'sub_id',
                'category_id',
                'name',
                'risk_description',
                'affected_properties',
                'treatment',
            ])->toArray();

        if (count($risks) == 0) {
            return redirect()->back()->withErrors('No any risk selected to proceed.');
        }

        // Deleting before Risk Setup
        $this->resetRiskSetup();

        /* MAX EXECUTION TIME */
        $byMethod = "";
        set_time_limit(40);

        foreach ($risks as $risk) {
            $registeredRisk = RiskRegister::create($risk);
            $registeredRisk->is_complete = 0;
            $registeredRisk->save();
            if (isset($request->is_map) && $request->is_map == 1) {
                $byMethod="Map";
                if ($request->control_mapping_project) {
                    // mapping compliance controls
                    $matchedControl = ProjectControl::where('project_id', $request->control_mapping_project)
                        ->where('applicable', 1)
                        ->where('primary_id', $risk['primary_id'])
                        ->where('sub_id', $risk['sub_id'])
                        ->first();

                    /* Closing risk if matched control is implemented */
                    if ($matchedControl->status == 'Implemented') {
                        $registeredRisk['status'] = 'Close';
                        $registeredRisk['residual_score'] = $riskAcceptableScore->score;
                        $registeredRisk->update();
                    }


                    $mappingControl = [
                        'risk_id' => $registeredRisk->id,
                        'control_id' => $matchedControl->id,
                    ];
                    RiskMappedComplianceControl::insert($mappingControl);
                }
            }
        }
        return redirect()->route('risks.register.index')->withSuccess($byMethod == "Map"?'Risks mapped successfully':'Risks added successfully.');
    }

    // Deleting before Risk Setup
    protected function resetRiskSetup()
    {
        RiskRegister::whereNull('deleted_at')->delete();
    }
}
