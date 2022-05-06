<?php

namespace App\Http\Controllers\RisksManagement\RiskRegister;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Compliance\Standard;
use App\Models\RiskManagement\RiskCategory;
use App\Models\RiskManagement\RiskRegister;
use App\Traits\RisksManagement\HelperMethodsTrait;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixScore;
use App\Rules\RiskManagement\ValidRiskAffectedProperties;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixImpact;
use App\Models\RiskManagement\RiskMatrix\RiskScoreLevelType;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixLikelihood;
use Inertia\Inertia;

class RiskRegisterReactController
{
    use HelperMethodsTrait;

    public function __construct()
    {
    }

    public function index(Request $request)
    {

        $riskCategories = RiskCategory::query()->withCount(['registerRisks as total_risks' => function (Builder $query) use ($request) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
            $query->when($request->has('only_incomplete') && $request->only_incomplete === 'true', function (Builder $query) {
               $query->where('is_complete', 0);
            });
        }])
            ->having('total_risks', '>', 0)
            ->withCount(['registerRisks as total_incomplete_risks' => function (Builder $query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
                $query->where('is_complete', 0);
            }])->get();
        return response()->json(['success' => true,'data'=>$riskCategories]);
    }

    public function riskUpdate(Request $request, $Id)
    {
        $inputValidationRules = [
            'affected_properties' => [
                'required',
                'max:150',
                new ValidRiskAffectedProperties(),
            ],
            'treatment_options' => 'required|in:Mitigate,Accept',
            'likelihood' => 'required',
            'impact' => 'required',
        ];

        $riskInputs = [];

        $input = $request->toArray();

        if ($request->has('affected_functions_or_assets')) {
            $riskInputs['affected_functions_or_assets'] = $input['affected_functions_or_assets'];

            // validation rule
            $inputValidationRules['affected_functions_or_assets'] = 'required|max:150';
        }

        if ($request->treatment_options) {
            $riskInputs['treatment_options'] = $input['treatment_options'];
        }

        $request->validate($inputValidationRules);

        $risk = RiskRegister::find($Id);

        $affectedProperties = $input['affected_properties'];

        $riskScore = RiskMatrixScore::where('likelihood_index', $input['likelihood'])->where('impact_index', $input['impact'])->first();


        $riskInputs = array_merge($riskInputs, [
            'affected_properties' => $affectedProperties,
            'treatment_options' => $input['treatment_options'],
            'likelihood' => $input['likelihood'] + 1,
            'impact' => $input['impact'] +1,
            'inherent_score' => $riskScore->score,
            'residual_score' => $riskScore->score,
            'is_complete' => 1,
        ]);

        $risk->update($riskInputs);

        return redirect()->back();

    }

    public function riskShow($id)
    {
        /* Getting compliance standards */
        $data['allComplianceStandards'] = Standard::whereHas('projects')->get();

        $data['risk'] = RiskRegister::with('category')->findOrFail($id);
        return response()->json(['success' => true,'data'=>$data]);
    }

    /**
     * get standard filter options for map controls risk register show
     */
    public function getFilterOptions(Request $request)
    {
        $data=[];
        $allStandards = Standard::whereHas('projects')->get();
        //manage data for dropdown
        $managedStandards[] = [
            'value' => 0,
            'label' => "Select Standard"
        ];
        foreach ($allStandards as $key => $eachStandard) {
            $managedStandards[] = ['value'=>$eachStandard['id'],'label'=>$eachStandard['name']];
        }
        $data['managedStandards']=$managedStandards;
        $data['projects']=[];
        if($request->standardId){
            $projects = [];
            $standardId = $request->standardId;
            $standard = Standard::find($standardId);

            if ($standard) {
                $managedProjects[] = [
                    'value' => 0,
                    'label' => "Select Project"
                ];
                $projects = $standard->projects()->get();
                foreach ($projects as $key => $eachProject) {
                    $managedProjects[] = ['value'=>$eachProject['id'],'label'=>$eachProject['name']];
                }
                $data['projects']=$managedProjects;
            }
        }

        return response()->json(['data'=>$data]);

    }

    public function registeredRisks($id, Request $request){
        $risks = RiskRegister::query()
            ->where('category_id', $id)
            ->where('name', 'LIKE', '%'. $request->search .'%')
            ->where('name', 'LIKE', '%' . $request->search_within_category . '%')
            ->when($request->only_incomplete === 'true', function (Builder $query) {
                $query->where('is_complete', '=',  0);
            })
            ->with(['mappedControls', 'riskMatrixLikelihood', 'riskMatrixImpact'])
            ->paginate(5);

        return response()->json($risks);
    }
}
