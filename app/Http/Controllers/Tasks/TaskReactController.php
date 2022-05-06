<?php

namespace App\Http\Controllers\Tasks;

use Auth;
use Illuminate\Http\Request;
use App\Utils\RegularFunctions;
use App\Exports\Tasks\TaskExport;
use App\Models\Compliance\Project;
use App\Models\Compliance\Standard;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\Admin;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\ValidDataScope;
use App\Models\Compliance\ProjectControl;
use App\Models\UserManagement\AdminDepartment;
use App\Models\Administration\OrganizationManagement\Department;
use App\Models\Administration\OrganizationManagement\Organization;

class TaskReactController extends Controller
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

    // public function getPageData(Request $request)
    // {
    //     $taskListURL = '';
    //     $taskContributors = RegularFunctions::getControlContributorList();
    //     $authUser = $this->loggedInUser;
    //     $urlSegmentTwo = request()->segment(1);
    //     $responsibleView = false;
    //     $approverView = false;
    //     $currentPage = request()->segment(3);
    //     $complianceProjects = Project::all();
    //     $departments = Department::all();
    //     $organization = Organization::first();
    //     $selectedDepartments = [];

    //     if ($request->has('selected_projects')) {
    //         $selectedProjects = explode(',', $request->selected_projects);
    //     } else {
    //         $selectedProjects = $complianceProjects->pluck(['id'])->toArray();
    //     }

    //     /* For selecting deparments in task monitor page */
    //     if ($request->segment(1) == 'global') {
    //         if ($request->has('selected_departments')) {
    //             $selectedDepartments = explode(',', $request->selected_departments);
    //         } else {
    //             $selectedDepartments =  $departments->pluck(['id'])->toArray();
    //         }
    //     } else {
    //         /* In case of compliance task monitor*/
    //         if (!is_null($authUser->department)) {
    //             if (is_null($authUser->department->department_id)) {
    //                 $selectedDepartments = [0];
    //             } else {
    //                 $selectedDepartments = [$authUser->department->department_id];
    //             }
    //         }
    //     }

    //     if ($urlSegmentTwo == 'global') {
    //         $taskListURL = route('global.tasks.my-tasks-json-data');
    //     } else {
    //         $taskListURL = route('compliance.tasks.my-tasks-json-data');

    //         if ($currentPage == 'all-active' || $currentPage == 'due-today' || $currentPage == 'pass-due' || $currentPage == 'under-review') {
    //             $responsibleView = true;
    //         } elseif ($currentPage == 'need-my-approval') {
    //             $approverView = true;
    //         }
    //     }


    //     //Formatting Data for React Select
    //     $data = [];
    //     foreach ($complianceProjects as $key => $project) {
    //         $data[$key]['label']  = $project->name;
    //         $data[$key]['value']  = $project->id;
    //     }
    //     $complianceProjects = $data;

    //     $data = [];
    //     $i = 0;
    //     foreach ($taskContributors as $key => $taskContributor) {
    //         $data[$i]['label'] = $key;
    //         $data[$i]['value'] = $taskContributor;
    //         $i++;
    //     }
    //     $taskContributors = $data;

    //     array_unshift($taskContributors, ['label' => 'All Approver Users', 'value' => 0]);
    //     $approverUsers = $taskContributors;
    //     array_unshift($data, ['label' => 'All Assigned Users', 'value' => 0]);
    //     $assigneeUsers = $data;

    //     return response()->json([
    //         'approverView' => $approverView,
    //         'authUser' => $authUser,
    //         'currentPage' => $currentPage,
    //         'complianceProjects' => $complianceProjects,
    //         'departments' => $departments,
    //         'organization' => $organization,
    //         'responsibleView' => $responsibleView,
    //         'selectedProjects' => $selectedProjects,
    //         'selectedDepartments' => $selectedDepartments,
    //         'approverUsers' => $approverUsers,
    //         'assigneeUsers' => $assigneeUsers,
    //         'taskListURL' => $taskListURL,
    //         'urlSegmentTwo' => $urlSegmentTwo,
    //     ], 200);
    // }

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
        $selectedStatus = '';
        $selectedStage = '';
        $selectedProjects = request('selected_projects');

        // if ($request->has('selected_projects')) {
        //     $selectedProjects = explode(',', $request->selected_projects);
        // } else {
        //     $selectedProjects = $complianceProjects->pluck(['id'])->toArray();
        // }

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
                    $selectedDepartments = [0];
                } else {
                    $selectedDepartments = [$authUser->department->department_id];
                }
            }
        }

        if ($currentPage == 'all-active')
            $selectedStatus = 'active';
        elseif ($currentPage == 'due-today')
            $selectedStatus = 'due_today';
        elseif ($currentPage == 'pass-due')
            $selectedStatus = 'pass_due';
        elseif ($currentPage == 'under-review' || $currentPage == 'need-my-approval') {
            $selectedStage = 'Under Review';
            $selectedStatus = '';
        }

        if ($urlSegmentTwo == 'global') {
            $taskListURL = route('global.tasks.get-my-tasks-json-data');

            if ($currentPage == 'all-active' || $currentPage == 'due-today' || $currentPage == 'pass-due') {
                $taskListURL = $taskListURL . '?status=' . $selectedStatus . '&';
            } else {
                $taskListURL = $taskListURL . '?approval_status=' . $selectedStage . '&';
            }
        } else {
            $taskListURL = route('compliance.tasks.get-my-tasks-json-data');

            if ($currentPage == 'all-active' || $currentPage == 'due-today' || $currentPage == 'pass-due' || $currentPage == 'under-review') {
                // if ($selectedStatus == 'active' || $selectedStatus == 'due_today' || $selectedStatus == 'pass_due' || $selectedStage == 'under_review' || $selectedStage == 'Under Review') {
                $responsibleView = true;
                $taskListURL = $taskListURL . '?responsible_user=' . $authUser->id . '&';
                $taskListURL = $taskListURL . 'status=' . $selectedStatus . '&';
                $taskListURL = $taskListURL . 'approval_status=' . $selectedStage . '&';
            } elseif ($currentPage == 'need-my-approval') {
                $approverView = true;
                $taskListURL = $taskListURL . '?approver_user=' . $authUser->id . '&';
                $taskListURL = $taskListURL . 'approval_status=' . $selectedStage . '&';
                $taskListURL = $taskListURL . 'status=' . $selectedStatus . '&';
            }
        }

        if ($selectedProjects)
            $taskListURL = $taskListURL . 'selected_projects=' . $selectedProjects . '&';

        //Formatting Data for React Select
        $data = [];
        foreach ($complianceProjects as $key => $project) {
            $data[$key]['label']  = $project->name;
            $data[$key]['value']  = $project->id;
        }
        $complianceProjects = $data;

        $selectedProjects = Project::whereIn('id', explode(',', $selectedProjects))->get();
        $data = [];
        foreach ($selectedProjects as $key => $project) {
            $data[$key]['label']  = $project->name;
            $data[$key]['value']  = $project->id;
        }
        $selectedProjects = $data;

        $data = [];
        $i = 0;
        foreach ($taskContributors as $key => $taskContributor) {
            $data[$i]['label'] = $key;
            $data[$i]['value'] = $taskContributor;
            $i++;
        }
        $taskContributors = $data;

        array_unshift($taskContributors, ['label' => 'All Approvers', 'value' => 0]);
        $approverUsers = $taskContributors;
        array_unshift($data, ['label' => 'All Assignees', 'value' => 0]);
        $assigneeUsers = $data;

        return inertia('global-task-monitor/GlobalTaskMonitor', compact('approverView', 'authUser', 'currentPage', 'complianceProjects', 'departments', 'organization', 'responsibleView', 'selectedProjects', 'selectedDepartments', 'selectedStatus', 'selectedStage', 'taskContributors', 'assigneeUsers', 'approverUsers', 'taskListURL', 'urlSegmentTwo'));
    }

    public function myTasksJsonData(Request $request)
    {
        $page = $request->page ?? 1;
        $size = $request->per_page ?? 10;

        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;
        $currentPage = $request->currentPage;
        $tasks = [];

        $requestSelectedDepart = $request->selected_departments;

        $selectedDepartments = !is_null($requestSelectedDepart) ? ($requestSelectedDepart == 0 ? [0] : explode(',', $request->selected_departments)) : [];

        $tasksQuery = ProjectControl::query()
            ->where(function ($query) {
                $query->orWhereNotNull('responsible')->orWhereNotNull('approver');
            })
            ->when($request->segment(1) == "compliance", function ($query) {
                $query->withoutGlobalScopes();
            })
            ->leftJoin('compliance_projects', 'compliance_project_controls.project_id', '=', 'compliance_projects.id')
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
            })
            ->when($selectedDepartments, function ($query) use ($selectedDepartments) {
                if (in_array(0, $selectedDepartments)) {
                    $query->orWhereIn('approver_departments.department_id', $selectedDepartments)->orWhereNull('approver_departments.department_id');
                    $query->orWhereIn('responsible_departments.department_id', $selectedDepartments)->orWhereNull('responsible_departments.department_id');
                } else {
                    $query->orWhereIn('approver_departments.department_id', $selectedDepartments);
                    $query->orWhereIn('responsible_departments.department_id', $selectedDepartments);
                }
            });

        $count = $tasksQuery->count();
        $tasks = $tasksQuery->skip(--$page * $size)->take($size)
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
            ->paginate($size);

        foreach ($tasks as $task) {
            $task['type'] = '<span class="badge task-status-green">Compliance</span>';
            $task['assigned'] = $task->responsible_first_name . ' ' . $task->responsible_last_name;
            $task['approver'] = $task->approver_first_name . ' ' . $task->approver_last_name;

            if ($task->status == 'Implemented') {
                $task['completion_date'] = date('jS F, Y', strtotime($task->approved_at));
                $task['due_date'] = date('jS F, Y', strtotime($task->deadline));
            } else {
                $task['due_date'] = date('jS F, Y', strtotime($task->deadline));
            }

            //Active
            if (($task->deadline > date('Y-m-d')) && $task->status != 'Implemented') {
                $task['task_status'] = '<span class="badge task-status-black">Upcoming</span>';
            }

            // Pass due
            if (($task->deadline < date('Y-m-d')) && $task->status != 'Implemented') {
                $task['task_status'] = '<span class="badge task-status-red">Past Due</span>';
            }

            // Due today
            if (($task->deadline == date('Y-m-d')) && $task->status != 'Implemented') {
                $task['task_status'] = '<span class="badge task-status-orange">Due Today</span>';
            }

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

            // $implementationStatus = [
            //     'Not Implemented' => 'task-status-red',
            //     'Rejected' => 'task-status-orange',
            //     'Under Review' => 'task-status-blue',
            //     'Implemented' => 'task-status-green',
            // ][$task->status];

            $task['approval_stage'] = "<span class='badge " . $implementationStatus . "'>" . $task->status . '</span>';

            if ($task->status == 'Implemented') {
                $task['task_status'] = "<span class='badge " . $implementationStatus . "'>" . $task->status . '</span>';
            }

            $task['action'] = '<a href="' . route('compliance-project-control-show', [$task->project_id, $task->id, 'tasks']) . '" class="btn btn-primary go">Go </a>';
        }

        $render = [];

        foreach ($tasks as $task) {
            $satifiedData = '';
            $dueDate = '';
            $module = '<span class="badge task-status-green">Compliance</span>';
            $status = '';

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

        return response()->json([
            'data' => $tasks,
            'total' => $count,
        ]);

        // $response = [
        //     'draw' => $draw,
        //     'recordsTotal' => $count,
        //     'recordsFiltered' => $count,
        //     'data' => $render,
        // ];

        // return response()->json($response);
    }

    public function getProjects(Request $request)
    {
        $request->validate([
            'data_scope' => 'required'
        ]);

        $projects = Project::select('id', 'name')->orderBy('id', 'DESC')->get();
        $selectedProjects = implode(',', $projects->pluck(['id'])->toArray());

        //Formatting Data for React Select
        $data = [];
        foreach ($projects as $key => $project) {
            $data[$key]['label']  = $project->name;
            $data[$key]['value']  = $project->id;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'projects' => $data,
                'selected_projects' => $selectedProjects,
            ]);
        }
    }

    public function getUsersByDepartment(Request $request)
    {
        if ($request->data_scope) {
            $dataScope = explode('-', $request->data_scope);
            $organizationId = $dataScope[0];
            $departmentId = $dataScope[1];
            $all_child_department_with_own_department = Department::where('parent_id', $departmentId)
                ->orWhere('id', $departmentId)
                ->pluck('id');

            $admins = Admin::select('id', 'first_name', 'last_name')
                ->where('status', 'active')
                ->whereHas('roles', function ($query) {
                    return $query->whereIn('name', ['Global Admin', 'Compliance Administrator', 'Contributor']);
                })
                ->whereHas('department', function ($query) use ($departmentId, $organizationId, $all_child_department_with_own_department) {
                    if ($departmentId == 0 && $organizationId != null)
                        return $query->where('organization_id', $organizationId);
                    else
                        return $query->whereIn('department_id', $all_child_department_with_own_department);
                })
                ->get();

            $options_for_select = [];
            foreach ($admins as $admin) {
                $option['value'] = $admin->id;
                $option['label'] = $admin->full_name;
                array_push($options_for_select, $option);
            }

            return response()->json($options_for_select);
        }
    }

    public function getContributorsList()
    {
        if (request()->has('editable') && request()->input('editable') === "1") {
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
        } else {
            $contributors = Admin::where('status', 'active')
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

    public function getAllProjects()
    {
        $projects = Project::withoutGlobalScopes()->select('id', 'name')->orderBy('id', 'DESC')->get();

        //Formatting Data for React Select
        $data = [];
        foreach ($projects as $key => $project) {
            $data[$key]['label'] = $project->name;
            $data[$key]['value'] = $project->id;
        }

        return response()->json([
            'success' => true,
            'projects' => $data,
        ]);
    }

    public function getAllProjectFilterDataWithoutDataScope(Request $request)
    {
        $request->validate([
            'selected_departments' => 'nullable'
        ]);

        $selectedDepartments = array_filter(explode(',', request('selected_departments')));

        $projects = Project::withoutGlobalScopes()
            ->whereHas('department', function ($query) use ($selectedDepartments) {
                $query->whereIn('department_id', $selectedDepartments)->orWhereNull('department_id');
            });
        
        if($request->compliance_filter){
            $projects->whereHas('controls',function ($query) {
                $query->where('approver', \Auth::user()->id)->orWhere('responsible',\Auth::user()->id);
            });
        }
        $projects=$projects->get();
        
        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }
}
