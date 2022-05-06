<?php

namespace App\Http\Controllers\Compliance\Project;

use App\Exports\Project\ProjectExport;
use App\Http\Controllers\Controller;
use App\Utils\RegularFunctions;
use App\Mail\Compliance\ControlAssignmentRemoval;
use App\Mail\Compliance\ProjectNameUpdateNotification;
use App\Models\Compliance\Project;
use Inertia\Inertia;
use App\Models\Compliance\ProjectControl;
use App\Models\Compliance\Standard;
use App\Rules\common\UniqueWithinDataScope;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\ValidDataScope;
use Illuminate\Validation\Rules\Unique;

class ProjectController extends Controller
{
    protected $loggedUser;
    protected $viewBasePath = 'compliance.projects.';

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            $this->loggedUser = Auth::guard('admin')->user();

            return $next($request);
        });

        \View::share('standards', RegularFunctions::getAllStandards());
    }

    public function view(Request $request)
    {
        /* Sharing page title to view */
        view()->share('pageTitle', 'View Projects');

        return Inertia::render('compliance/project-list-page/ProjectListPage');
    }

    /***
     * @retun html
     * get list of projects
     */
    protected function getProjectList(Request $request)
    {
        $request->validate([
            'data_scope' => 'required'
        ]);

        $projectBaseQuery = Project::where(function ($query) use ($request) {
            if ($request->project_name) {
                $query->where('name', 'like', '%'.$request->project_name.'%');
            }
        })->withCount("applicableControls")
            ->withCount("implementedControls")
                ->withCount("notImplementedControls");


        if ($this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            $projects = $projectBaseQuery->orderBy('id', 'DESC')->get();
        } else {
            $projects = $projectBaseQuery->whereHas('controls', function ($q) {
                $q->where('approver', $this->loggedUser->id);
                $q->orWhere('responsible', $this->loggedUser->id);

            })->orderBy('id', 'DESC')->get();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $projects,
            ]);
        }
    }

    /***
     * @retun Create project form
     *
     */
    public function create()
    {
        $project = new Project();
        Log::info('User is attempting to create a compliance project.');

        return Inertia::render('compliance/project-create-page/ProjectCreatePage', ['project' => $project]);
    }

    /**
     * Method edit
     *
     * @param Request $request [explicite description]
     * @param $id $id [explicite description]
     *
     * @return void
     */
    public function edit(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $assignedControls = $project->controls()->whereNotNull('responsible')->whereNotNull('approver')->count();
        Log::info('User is attempting to edit a compliance project.', [
            'project_id' => $id
            ]);

        return Inertia::render('compliance/project-create-page/ProjectCreatePage', ['project' => $project, 'assignedControls' => $assignedControls]);
    }

    /**
     * get edit data
    */
    public function getEditData(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $assignedControls = $project->controls()->whereNotNull('responsible')->whereNotNull('approver')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'project' => $project,
                'assignedControls' => $assignedControls
            ]
        ]);
    }

    /*
    * Creates a new project
    */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required','max:190', new UniqueWithinDataScope(new Project, 'name')],
            'description' => 'required',
            'standard_id' => 'required|numeric',
        ], [
            'name.required' => 'The Project Name field is required',
            'description.required' => 'The Description field is required',
            'standard_id.required' => 'The Standard field is required',
        ]);

        $inputs = $request->all();
        $project = \DB::transaction(function () use ($request, $inputs) {
            $standard = Standard::findorfail($request->standard_id);

            /*Creating project*/
            $project = Project::create([
                'standard_id' => $standard->id,
                'standard' => $standard->name,
                'name' => $inputs['name'],
                'description' => $inputs['description'],
            ]);

            $controls = $standard->controls()->get(['name', 'primary_id', 'sub_id', 'id_separator', 'description','required_evidence'])->toArray();

            /*Creating project controls*/
            $project->controls()->createMany($controls);

            return $project;
        });

        Log::info('User has created a compliance project.', ['project_id' => $project->id]);

        return redirect(route('compliance-project-show', $project->id));
    }

    /***
     * Update a project
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required','max:190', new UniqueWithinDataScope(new Project, 'name', $id)],
            'description' => 'required',
            'standard_id' => 'required|numeric',
        ], [
            'name.required' => 'The Project Name field is required',
            'description.required' => 'The Description field is required',
            'standard_id.required' => 'The Standard field is required',
        ]);


        $project = Project::findOrFail($id);
        $currentProjectName = decodeHTMLEntity($project->name);
        $newProjectName = $request->name;

        $assignedControls = ProjectControl::where('project_id', $project->id)->whereNotNull('responsible')->whereNotNull('approver')->get();

        //checking if old project name and new project name is not same and send notification to users
        if ($currentProjectName != $newProjectName && $assignedControls->count() > 0) {
            /* Getting unique control by responsible and approvar */
            $uniqueResponsibleProjectControls = $assignedControls->unique('responsible');
            $uniqueApproverProjectControls = $assignedControls->unique('approver');

            /* Sending responsible users mail notification of update project name */
            foreach ($uniqueResponsibleProjectControls as $uniqueResponsibleProjectControl) {
                $controls = $assignedControls->where('responsible', $uniqueResponsibleProjectControl->responsible);

                $data = [
                    'greeting' => 'Hello '.decodeHTMLEntity($uniqueResponsibleProjectControl->responsibleUser->full_name),
                    'title' => 'The name of the Project '.'<b> '.$currentProjectName.'</b>'.' has been renamed to '.'<b> '.decodeHTMLEntity($newProjectName).'</b>'.'. The control(s) for which you are assigned as responsible for are listed below:',
                    'project' => $project,
                    'standard' => $project->standard,
                    'projectName' => $newProjectName,
                    'projectControls' => $controls,
                ];

                Mail::to($uniqueResponsibleProjectControl->responsibleUser->email)->send(new ProjectNameUpdateNotification($data));
            }

            /* Sending approver users mail notification of update project name */
            foreach ($uniqueApproverProjectControls as $uniqueApproverProjectControl) {
                $controls = $assignedControls->where('approver', $uniqueApproverProjectControl->approver);

                $data = [
                    'greeting' => 'Hello '.decodeHTMLEntity($uniqueApproverProjectControl->approverUser->full_name),
                    'title' => 'The name of the Project '.'<b> '.decodeHTMLEntity($currentProjectName).'</b>'.' has been renamed to '.'<b> '.decodeHTMLEntity($newProjectName).'</b>'.'. The control(s) for which you are assigned as an approver are listed below:',
                    'project' => $project,
                    'standard' => $project->standard,
                    'projectName' => $newProjectName,
                    'projectControls' => $controls,
                ];

                Mail::to($uniqueApproverProjectControl->approverUser->email)->send(new ProjectNameUpdateNotification($data));
            }
        }

        $input = $request->all();

        $updatedProject = \DB::transaction(function () use ($request, $project, $input) {
            $assignedControls = $project->controls()->whereNotNull('responsible')->whereNotNull('approver')->count();
            $isAllowedToUpdateStandard = (($project->standard_id != $input['standard_id']) && $assignedControls == 0);

            $toBeUpDatedInput = [
                "name" => $input['name'],
                "description" => $input['description'],
                "data_scope" => $input['data_scope']
            ];

            /* updating the project standard if project controls are not assigned */
            if ( $isAllowedToUpdateStandard) {
                $newStandard = Standard::findOrFail($input['standard_id']);
                $toBeUpDatedInput["standard_id"] = $newStandard->id;
                $toBeUpDatedInput["standard"] = $newStandard->name;
            }

            $updatedProject = $project->update($toBeUpDatedInput);

            /* updating the project standard control if project controls are not assigned */
            if ($isAllowedToUpdateStandard) {
                $standard = Standard::findorfail($request->standard_id);

                /* Deleting old controls */
                $project->controls()->delete();

                /* Saving new controls */
                $controls = $standard->controls()->get(['name', 'primary_id', 'sub_id', 'id_separator', 'description'])->toArray();

                $project->controls()->createMany($controls);
            }

            return $updatedProject;
        });

        Log::info('User has updated a compliance project.', ['project_id' => $id]);

        return redirect()->route('compliance-project-show', $project->id);
    }

    /***
     * Deletes the project
     */
    public function delete(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $assignedControls = ProjectControl::where('project_id', $project->id)->whereNotNull('responsible')->whereNotNull('approver')->get();

        if ($assignedControls->count() > 0) {
            /* Getting unique control by responsible and approvar */
            $uniqueResponsibleProjectControls = $assignedControls->unique('responsible');
            $uniqueApproverProjectControls = $assignedControls->unique('approver');

            /* Sending responsible users mail notification of un-assigment */
            foreach ($uniqueResponsibleProjectControls as $uniqueResponsibleProjectControl) {
                $controls = $assignedControls->where('responsible', $uniqueResponsibleProjectControl->responsible);
                $subjects = 'Removal of task assignment';
                $data = [
                    'greeting' => 'Hello '.decodeHTMLEntity($uniqueResponsibleProjectControl->responsibleUser->full_name),
                    'title' => 'You have been removed responsibility for providing evidence for the following tasks:',
                    'project' => $project,
                    'projectControls' => $controls,
                    'information' => "This is an informational email and you don't have to take any action.",
                ];

                Mail::to($uniqueResponsibleProjectControl->responsibleUser->email)->send(new ControlAssignmentRemoval($data, $subjects));
            }

            /* Sending approver users mail notification of un-assigment */
            foreach ($uniqueApproverProjectControls as $uniqueApproverProjectControl) {
                $controls = $assignedControls->where('approver', $uniqueApproverProjectControl->approver);
                $subjects = 'Removal of approval responsibility';
                $data = [
                    'greeting' => 'Hello '. decodeHTMLEntity($uniqueApproverProjectControl->approverUser->full_name),
                    'title' => 'You have been removed as an approver from the following tasks:',
                    'project' => $project,
                    'projectControls' => $controls,
                    'information' => "This is an informational email and you don't have to take any action.",
                ];

                Mail::to($uniqueApproverProjectControl->approverUser->email)->send(new ControlAssignmentRemoval($data, $subjects));
            }
        }

        $project->delete();
        Log::info('User has deleted a compliance project.', ['project_id' => $id]);

        return redirect()->back()->with(['success' => 'Project deleted successfully.']);
    }

    public function checkProjectNameTaken(Request $request, $projectId = null)
    {
        $validator = Validator::make($request->all(), [
            'name' => [new UniqueWithinDataScope(new Project, 'name', $projectId)],
        ]);

        if ($validator->fails()) {
            return 'false';
        } else {
            return 'true';
        }
    }

    public function projectExport(Request $request)
    {
        $fileName = 'Compliance Project '.date('d-m-Y').'.xlsx';
        Log::info('User has exported a compliance project.', ['project_id' => $request->id]);

        return Excel::download(new ProjectExport($request->id), $fileName);
    }
}
