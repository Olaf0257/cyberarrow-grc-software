<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Utils\RegularFunctions;
use App\Models\Compliance\Project;
use App\Models\Compliance\ProjectControl;
use App\Models\Compliance\Standard;
use Auth;
use Illuminate\Http\Request;
use App\Models\Administration\OrganizationManagement\Department;
use App\Models\Administration\OrganizationManagement\Organization;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Tasks\TaskExport;

class TaskController extends Controller
{
    protected $view_path = 'tasks.';
    protected $loggedInUser;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->loggedInUser = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // return view('app');
        $taskListURL = '';
        $taskContributors = RegularFunctions::getControlContributorList();
        $authUser = $this->loggedInUser;
        $urlSegmentTwo = request()->segment(1);
        $responsibleView = false;
        $approverView = false;
        $currentPage = request()->segment(3);
        $complianceProjects = Project::all();
        $departments = Department::all();
        $organization = Organization::first();
        $selectedDepartments = [];

        if ($request->has('selected_projects')) {
            $selectedProjects = explode(',', $request->selected_projects);
        } else {
            $selectedProjects = $complianceProjects->pluck(['id'])->toArray();
        }

        /* For selecting deparments in task monitor page */
        if ($request->segment(1) == 'global') {
            if ($request->has('selected_departments')) {
                $selectedDepartments = explode(',', $request->selected_departments);
            } else {
                $selectedDepartments =  $departments->pluck(['id'])->toArray();
            }
        } else {
            /* In case of compliance task monitor*/
            if (!is_null($authUser->department)) {
                if (is_null($authUser->department->department_id)) {
                    $selectedDepartments =  [0];
                } else {
                    $selectedDepartments =  [$authUser->department->department_id];
                }
            }
        }

        if ($urlSegmentTwo == 'global') {
            $taskListURL = route('global.tasks.my-tasks-json-data');
        } else {
            $taskListURL = route('compliance.tasks.my-tasks-json-data');

            if ($currentPage == 'all-active' || $currentPage == 'due-today' || $currentPage == 'pass-due' || $currentPage == 'under-review') {
                $responsibleView = true;
            } elseif ($currentPage == 'need-my-approval') {
                $approverView = true;
            }
        }


        //Formatting Data for React Select
        // $data = [];
        // foreach ($complianceProjects as $key => $project) {
        //     $data[$key]['label']  = $project->name;
        //     $data[$key]['value']  = $project->id;
        // }
        // $complianceProjects = $data;

        // $data = [];
        // $i = 0;
        // foreach ($taskContributors as $key => $taskContributor) {
        //     $data[$i]['label'] = $key;
        //     $data[$i]['value'] = $taskContributor;
        //     $i++;
        // }
        // $taskContributors = $data;

        // array_unshift($taskContributors, ['label' => 'All Approvers', 'value' => 0]);
        // $approverUsers = $taskContributors;
        // array_unshift($data, ['label' => 'All Assignees', 'value' => 0]);
        // $assignedUsers = $data;

        // return inertia('global-task-monitor/GlobalTaskMonitor', compact('approverView', 'authUser', 'currentPage', 'complianceProjects', 'departments', 'organization', 'responsibleView', 'selectedProjects', 'selectedDepartments', 'taskContributors', 'assignedUsers', 'approverUsers', 'taskListURL', 'urlSegmentTwo'));

        return view($this->view_path . 'index', compact('approverView', 'authUser', 'currentPage', 'complianceProjects', 'departments', 'organization', 'responsibleView', 'selectedProjects', 'selectedDepartments', 'taskContributors', 'taskListURL', 'urlSegmentTwo'));
    }

    public function myTasksJsonData(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;
        $currentPage = $request->currentPage;
        $tasks = [];

        $requestSelectedDepart = $request->selected_departments;

        $selectedDepartments = !is_null($requestSelectedDepart) ? ($requestSelectedDepart == 0 ? [0] : explode(',', $request->selected_departments)) : [];

        $tasksQuery = ProjectControl::leftJoin('compliance_projects', 'compliance_project_controls.project_id', '=', 'compliance_projects.id')
            ->leftJoin('admins as approver', 'compliance_project_controls.approver', '=', 'approver.id')
            ->leftJoin('admins as responsible', 'responsible', '=', 'responsible.id')
            ->leftJoin('admin_departments as approver_departments', 'approver.id', '=', 'approver_departments.admin_id')
            ->leftJoin('admin_departments as responsible_departments', 'responsible.id', '=', 'responsible_departments.admin_id')
            ->where(function ($q) use ($request) {
                if ($request->has('selected_projects')) {
                    $q->whereIn('compliance_projects.id', explode(',', $request->selected_projects));
                }


                if ($request->project_name) {
                    $q->where('compliance_projects.name', 'LIKE', '%' . $request->project_name . '%');
                }

                if ($request->standard_name) {
                    $q->where('compliance_projects.standard', 'LIKE', '%' . $request->standard_name . '%');
                }

                if ($request->control_name) {
                    $q->where('compliance_project_controls.name', 'LIKE', '%' . $request->control_name . '%');
                }

                if ($request->status) {
                    // handling filters

                    if ($request->status == 'active') {
                        $q->where('deadline', '>', date('Y-m-d'));
                        $q->where('compliance_project_controls.status', '!=', 'Implemented');
                    }

                    if ($request->status == 'due_today') {
                        $q->where('deadline', date('Y-m-d'));
                        $q->where('compliance_project_controls.status', '!=', 'Implemented');
                    }

                    if ($request->status == 'pass_due') {
                        $q->where('deadline', '<', date('Y-m-d'));
                        $q->where('compliance_project_controls.status', '!=', 'Implemented');
                    }

                    if ($request->status == 'under_review') {
                        $q->where('compliance_project_controls.status', 'Under Review');
                    }

                    if ($request->status == 'need-my-approval') {
                        $q->where('compliance_project_controls.status', 'Under Review');
                    }
                }

                // handling filters
                if ($request->responsible_user) {
                    $q->where('responsible', $request->responsible_user);
                }

                if ($request->approver_user) {
                    $q->where('approver', $request->approver_user);
                }

                if ($request->due_date) {
                    $q->where('deadline', $request->due_date);
                }

                if ($request->completion_date) {
                    $q->where('approved_at', $request->completion_date);
                }

                if ($request->approval_status) {
                    $q->where('compliance_project_controls.status', $request->approval_status);
                }
            })->where(function ($query) use ($selectedDepartments) {
                if (in_array(0, $selectedDepartments)) {
                    $query->orWhereIn('approver_departments.department_id', $selectedDepartments)->orWhereNull('approver_departments.department_id');
                    $query->orWhereIn('responsible_departments.department_id', $selectedDepartments)->orWhereNull('responsible_departments.department_id');
                } else {
                    $query->orWhereIn('approver_departments.department_id', $selectedDepartments);
                    $query->orWhereIn('responsible_departments.department_id', $selectedDepartments);
                }
            });



        $count = $tasksQuery->count();
        $tasks = $tasksQuery->offset($start)->take($length)
            ->select([
                'deadline',
                'compliance_project_controls.status',
                'compliance_projects.standard as standard_name',
                'approved_at',
                'compliance_project_controls.id as id',
                'compliance_project_controls.name as control',
                'compliance_project_controls.description as control_description',
                'compliance_projects.id as project_id',
                'compliance_projects.name as project_name',
                'responsible.first_name as responsible_first_name',
                'responsible.last_name as responsible_last_name',
                'approver.first_name as approver_first_name',
                'approver.last_name as approver_last_name',
            ])
            ->get();

        $render = [];

        foreach ($tasks as $task) {
            $satifiedData = '';
            $dueDate = '';
            $module = '<span class="badge task-status-green">Compliance</span>';
            $status = '';
            $today = date('Y-m-d');

            if ($task->status == 'Implemented') {
                $satifiedData = date('jS F, Y', strtotime($task->approved_at));
            } else {
                $dueDate = date('jS F, Y', strtotime($task->deadline));
            }

            //Active
            if (($task->deadline > date('Y-m-d')) && $task->status != 'Implemented') {
                $status = '<span class="badge task-status-black">Upcoming</span>';
            }

            // Pass due
            if (($task->deadline < date('Y-m-d')) && $task->status != 'Implemented') {
                $status = '<span class="badge task-status-red">Past Due</span>';
            }

            // Due today
            if (($task->deadline == date('Y-m-d')) && $task->status != 'Implemented') {
                $status = '<span class="badge task-status-orange">Due Today</span>';
            }

            $projectName = $task->project_name;
            $controlName = $task->control;
            $standard = $task->standard_name;
            $name = $task->control . "  <b>$task->project_name</b>";
            $goToAction = '<a  href="' . route('compliance-project-control-show', [$task->project_id, $task->id, 'tasks']) . '" class="btn btn-success go">Go </a>';

            $implementationStatus = '';

            if ($task->status == 'Not Implemented') {
                $implementationStatus = 'task-status-red';
            }

            if ($task->status == 'Rejected') {
                $implementationStatus = 'task-status-orange';
            }

            if ($task->status == 'Under Review') {
                $implementationStatus = 'task-status-blue';
            }

            if ($task->status == 'Implemented') {
                $implementationStatus = 'task-status-green';
            }

            $render[] = [
                $projectName,
                $standard,
                $controlName,
                $task->control_description,
                $module,
                $task->responsible_first_name . ' ' . $task->responsible_last_name,
                $task->approver_first_name . ' ' . $task->approver_last_name,
                $satifiedData,
                $dueDate,
                $status,
                "<span class='badge " . $implementationStatus . "'>" . $task->status . '</span>',
                $goToAction,
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $render,
        ];

        return response()->json($response);
    }

    public function getProjectByStandards(Request $request)
    {
        $projects = [];
        $standardId = $request->standardId;
        $standard = Standard::find($standardId);

        if ($standard) {
            $projects = $standard->projects()->get();
        }

        return response($projects);
    }

    public function getProjectControlByProjects(Request $request)
    {
        $controls = [];

        $projectId = $request->projectId;

        $projectControls = ProjectControl::where('project_id', $projectId)->where('applicable', 1)->where('status', 'Implemented')->with('control')->get();

        if ($projectControls) {
            $controls = $projectControls;
        }

        return response($controls);
    }

    public function exportData(Request $request)
    {
        return Excel::download(new TaskExport(), 'tasks.csv');
    }
}
