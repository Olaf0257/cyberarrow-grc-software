<?php

namespace App\Http\Controllers\Compliance\Project;

use App\Rules\Compliance\AllowedEvidence;
use App\Rules\ValidateUrlOrNetworkFolder;
use Illuminate\Validation\Rule;
use Notification;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Utils\RegularFunctions;
use App\Models\Compliance\Comment;
use App\Models\Compliance\Control;
use App\Models\Compliance\Project;
use App\Models\DataScope\Scopable;
use Illuminate\Support\Facades\DB;
use App\Models\Compliance\Evidence;
use App\Models\Compliance\Standard;
use App\Models\DataScope\DataScope;
use App\Utils\AccessControlHelpers;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\Compliance\AssignTaskEmail;
use App\Models\Compliance\Justification;
use App\Models\Compliance\ProjectControl;
use App\Notifications\RemoveTaskNotification;
use App\Notifications\AssignedTaskNotification;
use App\Mail\Compliance\ControlAssignmentRemoval;
use App\Models\Administration\OrganizationManagement\Department;
use App\Models\TaskScheduleRecord\ComplianceProjectTaskScheduleRecord;

class ProjectControlController extends Controller
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

        \View::share('statuses', RegularFunctions::getProjectStatus());
        \View::share('frequencies', RegularFunctions::getFrequency());
        \View::share('contributors', RegularFunctions::getControlContributorList());
    }

    public function index(Request $request, Project $project, $tab = 'Details')
    {
        /* Access control */
        if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            $assignedProjectCount = Project::where('id', $project->id)->whereHas('controls', function ($q) {
                $q->where('approver', $this->loggedUser->id);
                $q->orWhere('responsible', $this->loggedUser->id);
            })->count();

            if ($assignedProjectCount == 0) {
                return RegularFunctions::accessDeniedResponse();
            }
            $control_disabled = true;
        } else {
            $control_disabled = false;
        }
        $controls = [];
        // return view('compliance.projects.controls.index', compact('project'));
        $data = [];
        $data['total'] = $project->controls()->count();
        $data['notApplicable'] = $project->controls()->where('applicable', 0)->count();
        $data['implemented'] = $project->controls()->where('applicable', 1)->where('status', 'Implemented')->count();
        $data['notImplementedcontrols'] = $project->controls()->where('applicable', 1)->where('status', 'Not Implemented')->count();
        $data['rejected'] = $project->controls()->Where('status', 'Rejected')->count();
        $data['notImplemented'] = $data['notImplementedcontrols'] + $data['rejected'];
        $data['underReview'] = $project->controls()->where('applicable', 1)->where('status', 'Under Review')->count();
        $data['perImplemented'] = ($data['total'] > 0) ? ($data['implemented'] / $data['total']) * 100 : 0;
        $data['perUnderReview'] = ($data['total'] > 0) ? ($data['underReview'] / $data['total']) * 100 : 0;
        $data['perNotImplemented'] = ($data['total'] > 0) ? ($data['notImplemented'] / $data['total']) * 100 : 0;
        return Inertia::render('compliance/project-details/ProjectDetails', ['project' => $project, 'controls' => $controls, 'data' => $data, 'control_disabled' => $control_disabled, 'tab' => ucfirst($tab)]);
    }


    private function getProjectControlAssignableUsers($project)
    {
        $projectDepart = $project->department;
        $departmentIds = [];

        if (is_null($projectDepart->department_id)) {
            $departments = Department::with(['departments' => function ($query) {
                $query->where('parent_id', 0);
            }])->get();
        } else {
            $departments = Department::where('id', $projectDepart->department_id)->with(['departments' => function ($query) use ($projectDepart) {
                $query->where('parent_id', $projectDepart->department_id);
            }])->get();
        }


        foreach ($departments as $key => $department) {
            $departmentIds[] = $department->id;

            $departmentIds = array_merge($departmentIds, $department->getAllChildDepartIds());
        }

        $users = Admin::where('status', 'active')->whereHas('department', function ($q) use ($departmentIds, $projectDepart) {
            $q->whereIn('department_id', $departmentIds);

            /* In case of top organization */
            if (is_null($projectDepart->department_id)) {
                $q->orWhereNull('department_id');
            }
        })->get();

        return $users;
    }

    /**
     * Project Controls.
     **/
    public function Controls(Request $request, Project $project)
    {
        $count = 0;
        $render = [];
        $draw = [];

        // filtring control for only loging user
        if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            $approverControls = $project->controls()->where('approver', $this->loggedUser->id)->get();
            $responsibleControls = $project->controls()->where('responsible', $this->loggedUser->id)->get();
            $controls = $approverControls->merge($responsibleControls);
            $count = $controls->count();
        } else {
            $projectControlsQuery = $project->controls();
            $count = $projectControlsQuery->count();
            // $controls = $projectControlsQuery->offset($start)->take($length)->get();
            $controls = $projectControlsQuery->paginate($request->per_page ? $request->per_page : 10);
        }

        $disabled = false;
        if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            $disabled = true;
        }

        $contributors = $this->getProjectControlAssignableUsers($project);

        // dd($contributors);
        //List of contributors
        $finalData = [];
        $finalData['responsibleUsers']['availableUsers'][] = ["value" => 0, "label" => "Select Responsible"];
        $finalData['approverUsers']['availableUsers'][] = ["value" => 0, "label" => "Select Approver"];

        foreach ($controls as $control) {

            $finalData['responsibleUsers']['default'] = ["value" => 0, "label" => "Select Responsible"];
            $finalData['approverUsers']['default'] = ["value" => 0, "label" => "Select Approver"];

            $action = route('compliance-project-control-show', [$project->id, $control->id]);
            if ($control->status == 'Not Implemented') {
                $status = "<span class='badge task-status-red w-60 '>" . $control->status . '**</span>';
            } elseif ($control->status == 'Implemented') {
                $status = "<span class='badge task-status-green w-60'>" . $control->status . '</span>';
            } elseif ($control->status == 'Rejected') {
                $status = "<span class='badge task-status-orange w-60'>" . $control->status . '</span>';
            } else {
                $status = "<span class='badge task-status-blue w-60'>" . $control->status . '</span>';
            }
            //            if(!$control->applicable)
            //                $status = "<span class='badge task-status-purple w-60'> Not Applicable</span>";

            if (!$control->is_editable) {
                $disabled = true;
            } else {
                $disabled = false;
            }

            foreach ($contributors as $key => $contributor) {
                $contributorId = $contributor->id;
                $contributorName = ucwords($contributor->first_name . ' ' . $contributor->last_name);
                $finalData['responsibleUsers']['availableUsers'][$contributorId] = ["value" => $contributorId, "label" => $contributorName];
                $finalData['approverUsers']['availableUsers'][$contributorId] = ["value" => $contributorId, "label" => $contributorName];
                if ($contributorId == $control->responsible) {
                    $finalData['responsibleUsers']['default'] = ["value" => $contributorId, "label" => $contributorName];
                }
                if ($contributorId == $control->approver) {
                    $finalData['approverUsers']['default'] = ["value" => $contributorId, "label" => $contributorName];
                }
            }

            $deadline = $control->deadline == null ? date('Y-m-d') : $control->deadline;

            $frequencies = RegularFunctions::getFrequency();

            $frequencyData = [];
            $frequencyData['isDisabled'] = $disabled;
            $frequencyData['defaultValue'] = ["value" => "One-Time", "label" => "One-Time"];
            foreach ($frequencies as $freq) {
                if ($freq == $control->frequency) {
                    $frequencyData['defaultValue'] = ["value" => $freq, "label" => $freq];
                    $frequencyData['options'][] = ["value" => $freq, "label" => $freq];
                } else {
                    $frequencyData['options'][] = ["value" => $freq, "label" => $freq];
                }
            }

            $controlName = "<a href='" . route('compliance-project-control-show', [$project->id, $control->id, 'tasks']) . "'>" . $control->name . '</a>';

            $isApplicable = $control->applicable ? true : false;

            $applicable = [
                'isApplicable' => $isApplicable,
                'applicableValue' => $control->id,
                'isDisabled' => $disabled,
            ];

            $finalData['responsibleUsers']['availableUsers'] = array_values($finalData['responsibleUsers']['availableUsers']);
            $finalData['approverUsers']['availableUsers'] = array_values($finalData['approverUsers']['availableUsers']);

            $render[] = [
                $applicable,
                $control->controlId,
                $controlName,
                $control->description,
                $status,
                $finalData["responsibleUsers"],
                $finalData["approverUsers"],
                $deadline,
                $frequencyData,
                $action
            ];

            $finalData['responsibleUsers']['availableUsers'] = [];
            $finalData['approverUsers']['availableUsers'] = [];
        }

        $controls->setCollection(collect($render));

        $response = [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $controls,
        ];
        echo json_encode($response);
    }

    /*
    |--------------------------------------------------------------------------
    | download control evidences
    |--------------------------------------------------------------------------
    */
    public function downloadEvidences(Request $request, Project $project, $projectControlId, $id, $linkedToControlId = null)
    {
        if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Contributor'])) {
            return RegularFunctions::accessDeniedResponse();
        }

        $document = Evidence::findorfail($id);

        $pcontrol = $document->projectControl;

        if ($this->loggedUser->hasAnyRole(['Contributor'])) {
            $allowed = $pcontrol->responsible == $this->loggedUser->id || $pcontrol->approver == $this->loggedUser->id;

            // when linked control evidences are downloaded
            if ($linkedToControlId) {
                $linkedToProjectControlEvidence = Evidence::where('path', $document->project_control_id)
                    ->where('project_control_id', $linkedToControlId)
                    ->where('type', 'control')
                    ->firstOrFail();
                $linkedToProjectControl = $linkedToProjectControlEvidence->projectControl;

                $linkedEvidenceAllowed = $linkedToProjectControl->responsible == $this->loggedUser->id || $linkedToProjectControl->approver == $this->loggedUser->id;

                if ($linkedEvidenceAllowed != true) {
                    exit;
                }
            } else {
                if ($allowed != true) {
                    exit;
                }
            }
        }
        $encryptedContents = Storage::get($document->path);

        $baseName = basename($document->path);
        $decryptedContents = decrypt($encryptedContents);
        $ext = pathinfo(storage_path() . '/' . $document->path, PATHINFO_EXTENSION);

        return response()->streamDownload(function () use ($decryptedContents) {
            echo $decryptedContents;
        }, $baseName);
    }

    /**
     * Controls evidences.
     **/
    public function evidences(Request $request, Project $project, ProjectControl $projectControl)
    {
        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;
        $evidences = Evidence::where('project_control_id', $projectControl->id)->orderBy('id', 'desc')->get();

        $render = [];
        foreach ($evidences as $evidence) {
            $evidenceType = $evidence->type;
            $evidenceName = $evidence->name;

            $urlLink = "<a class='btn btn-secondary btn-xs waves-effect waves-light' title='Download' href='" . route('compliance-project-control-evidences-download', [$project->id, $evidence->project_control_id, $evidence->id]) . "'><i class='fe-download' style='font-size:12px;'></i></a>";
            if ($evidence->type === "text") {
                $urlLink = "<button class='btn btn-secondary btn-xs waves-effect waves-light open-evidence-text-modal' title='Display' data-evidence-id='" . $evidence->id . "'><i class='fe-eye' style='font-size:12px;'></i></button>";
            }

            switch ($evidenceType) {
                case 'control':
                    $evidenceName = 'This control is linked to <a class="link-primary" href=' . route('project-control-linked-controls-evidences-view', [$project->id, $evidence->path, $evidence->project_control_id]) . ">{$evidence->name}
                                </a>
                                ";
                    $urlLink = "<a class='btn btn-secondary btn-xs waves-effect waves-light' title='Link' href='" . route('project-control-linked-controls-evidences-view', [$project->id, $evidence->path, $evidence->project_control_id]) . "'><i class='fe-link' style='font-size:12px;'></i></a>";
                    break;
                case 'link':
                    $urlLink = "<a class='btn btn-secondary btn-xs waves-effect waves-light' title='Link' href='" . $evidence->path . "' target='_blank'><i class='fe-link' style='font-size:12px;'></i></a>";
                    break;
            }

            if ($this->loggedUser->id == $projectControl->responsible) {
                if ($evidence->projectControl->status == 'Not Implemented' || $evidence->projectControl->status == 'Rejected') {
                    $deleteLink = "<a class='evidence-delete-link btn btn-danger btn-xs waves-effect waves-light' href='" . route('compliance-project-control-evidences-delete', [$project->id, $projectControl->id, $evidence->id]) . "' title='Delete'><i class='fe-trash-2' style='font-size:12px;'></i></a>";
                } else {
                    $deleteLink = '';
                }
            } else {
                $deleteLink = '';
            }

            $actions = "<div class='btn-group'>" . $urlLink . $deleteLink . '</div>';
            $render[] = [
                $evidenceName,
                date('j M Y', strtotime($evidence->deadline)),
                date('j M Y', strtotime($evidence->created_at)),
                $actions,
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => count($evidences),
            'recordsFiltered' => count($evidences),
            'data' => $render,
        ];

        return response()->json($response);
    }

    /*
    * Returns controls show page
    */
    public function show(Request $request, Project $project, ProjectControl $projectControl, $activeTabs = 'details')
    {
        $projectControlId = $projectControl->id;

        if (!AccessControlHelpers::viewProjectControlDetails($this->loggedUser, $projectControl)) {
            return RegularFunctions::accessDeniedResponse();
        }

        //getting standard having project only
        $allStandards = Standard::whereHas('projects')->get();

        $comments = Comment::where('project_control_id', $projectControlId)->with(['sender' => function ($q) {
            $q->select(['id', 'first_name', 'last_name']);
        }])->get();

        $frequencies = RegularFunctions::getFrequency();

        $evidence = Evidence::where('project_control_id', $projectControlId)->first();

        if ($evidence) {
            $latestJustification = Justification::where('project_control_id', $projectControlId)->with(['creator' => function ($q) {
                return $q->select(['id', 'first_name', 'last_name']);
            }])->latest('created_at')->first();
        } else {
            $latestJustification = null;
        }

        //$allowEvidencesUpload = $projectControl->status == 'Not Implemented' || $projectControl->status == 'Rejected' ? true : false;
        $allowEvidencesUpload = $projectControl->status == 'Not Implemented' ||
            $projectControl->status == 'Rejected' ||
            $projectControl->amend_status == "accepted" ||
            $projectControl->amend_status == "requested_approver";
        $nextReviewDate = RegularFunctions::nextReviewDate($projectControl);

        if (auth('admin')->user()->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            if (!$projectControl->applicable) {
                $disabled = true;
                $allowUpdate = false;
            } else {
                if (!$projectControl->is_editable) {
                    $disabled = true;
                    $allowUpdate = false;
                } else {
                    $disabled = false;
                    $allowUpdate = true;
                }
            }
        } else {
            $disabled = true;
            $allowUpdate = false;
        }

        // checking if the control is of the same department or not and if not disabling edit control
        $scope = Scopable::where([['scopable_id', $projectControlId], ['scopable_type', 'App\Models\Compliance\ProjectControl']])->first();
        $is_of_same_department = RegularFunctions::isAppScopeUserDepartment($scope);
        if (!$is_of_same_department) {
            $projectControl->is_editable = false;
            $disabled = true;
            $allowUpdate = false;
        }

        $meta = [
            'disabled' => $disabled,
            'update_allowed' => $allowUpdate,
            'evidence_upload_allowed' => $allowEvidencesUpload,
            'evidence_delete_allowed' => $this->loggedUser->id === $projectControl->responsible && ($projectControl->status == 'Not Implemented' || $projectControl->status == 'Rejected')
        ];

        // amend evidence stuff
        $justificationStatuses = [
            'Evidence amendment requested',
            'Evidence amendment request rejected',
            'Rejected'
        ];

        if($projectControl->status === 'rejected'){
            $justificationStatus = $justificationStatuses[2];
        } elseif($projectControl->amend_status === 'rejected') {
            $justificationStatus = $justificationStatuses[1];
        } else {
            $justificationStatus = $justificationStatuses[0];
        }

        return Inertia::render('compliance/project-controls/show/Index', compact(
            'project',
            'projectControl',
            'meta',
            'frequencies',
            'activeTabs',
            'nextReviewDate',
            'latestJustification',
            'allStandards',
            'comments',
            'justificationStatus'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | stores comment
    |--------------------------------------------------------------------------
    */
    public function storeComment(Request $request, Project $project, ProjectControl $projectControl)
    {
        $request->validate([
            'comment' => 'required',
        ]);

        $input = $request->toArray();

        // access control >> allowed to comment by responsible and approvar
        if (!($projectControl->responsible == $this->loggedUser->id || $projectControl->approver == $this->loggedUser->id)) {
            return RegularFunctions::accessDeniedResponse();
        }

        $comment = new Comment();

        $comment->project_control_id = $projectControl->id;
        $comment->comment = $input['comment'];

        if ($projectControl->responsible == $this->loggedUser->id) {
            $comment->from = $this->loggedUser->id;
            $comment->to = $projectControl->approver;
        } elseif ($projectControl->approver == $this->loggedUser->id) {
            $comment->from = $this->loggedUser->id;
            $comment->to = $projectControl->responsible;
        }

        $comment->save();

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Submitting controls for review
    |--------------------------------------------------------------------------
    */
    public function submitForReview(Request $request, Project $project, ProjectControl $projectControl)
    {
        $evidences = $projectControl->evidences;

        /* Only responsible users can submit*/
        if ($projectControl->responsible != $this->loggedUser->id) {
            return response()->json([
                'message' => 'Access Denied!',
            ]);
        }

        if (is_null($evidences) && count($evidences) == 0) {
            return response()->json([
                'message' => 'evidences not found!',
            ]);
        } else {
            if ($projectControl->status == 'Rejected') {
                $evidenceDocsUploadedAfterRejectionCount = $evidences->where('updated_at', '>', $projectControl->rejected_at)->count();

                if ($evidenceDocsUploadedAfterRejectionCount == 0) {
                    return redirect()->back()->withError('Access Denied');
                }
            }
        }

        $approver = Admin::findorfail($projectControl->approver);
        $responsible = Admin::findorfail($projectControl->responsible);

        if ($approver && $responsible) {

            $subject = 'Pending approval';

            $data = [
                'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($approver->full_name)),
                'content1' => 'Evidence has been submitted, and your approval is required for the following task:',
                'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                'content6' => '',
                'content7' => '',
                'email' => $approver->email,
                'action' => [
                    'action_title' => 'Click the below button to view the submitted evidence and to carry out the approval.',
                    'action_url' => route('compliance-project-control-show', [$project->id, $projectControl->id, 'tasks']),
                    'action_button_text' => 'Go to task details',
                ],
            ];

            // Queue::push(new EvidenceUploadedEmail($data));


            try {
                DB::beginTransaction();
                $initialAmendStatus = $projectControl->amend_status;
//                if($initialAmendStatus == "requested_approver" || $initialAmendStatus == "accepted"){
//                    $projectControl->amend_status = "submitted";
//                }

                $projectControl->amend_status = "submitted";
                $projectControl->status = 'Under Review';
                $projectControl->is_editable = 0;
                $projectControl->save();

                /* Changing the evidence(s) status */
                if(in_array($initialAmendStatus, ['requested_approver', 'requested_responsible', 'accepted']))
                {
                    Evidence::where('project_control_id', $projectControl->id)
                        ->where('status', 'initial')
                        ->update([
                            'status' => 'review'
                        ]);
                } else {
                    Evidence::where('project_control_id', $projectControl->id)->update([
                        'status' => 'review'
                    ]);
                }

                // when done commit
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();


                return response()->json([
                    'status' => false,
                    'message' => 'Oops something went wrong!',
                ]);
            }

            // updating `Submit To Approver Allowed Status`
            RegularFunctions::updateTaskEvidencesUploadAllowedStatus($projectControl->id, 0);

            /* Finding whether the current control has any linked evidence(s) */
            $linkedEvidenceStatus = Evidence::where([['project_control_id', $projectControl->id],['type','control']])->get();

            if($linkedEvidenceStatus->count() > 0){
                return $this->controlReviewApprove($request,$project,$projectControl,true);
            }

            /* Sending mail to assigned (approver) */
            Notification::route('mail', $data['email'])
                ->notify(new AssignedTaskNotification($data, $subject));

        }

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Updates the Compliance > projects >> controls >> update
    |--------------------------------------------------------------------------
    */
    public function updateAll(Request $request, Project $project)
    {
        if (!Auth::guard('admin')->user()->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            return RegularFunctions::accessDeniedResponse();
        }

        $input = $request->all();
        $projectControlIds = [];
        $inputsPerRows = [];

        if (!empty($input[1]) && $input[1] == "refreshData") {
            return redirect()->back();
        }

        if (count($input) > 0) {
            foreach ($input as $eachValue) {
                if (!is_null($eachValue['responsibleUser']) && !is_null($eachValue['approverUser'])) {
                    $inputsPerRows[] = [
                        'project_control_id' => $eachValue["controlId"],
                        'applicable' => (is_null($eachValue['checked']) || $eachValue['checked'] == true) ? 1 : 0,
                        'responsible' => $eachValue['responsibleUser']["value"],
                        'approver' => $eachValue['approverUser']["value"],
                        'deadline' => $eachValue['deadline'] != null ? $eachValue['deadline'] : date("Y-m-d"),
                        'frequency' => $eachValue['frequency'] != null ? $eachValue['frequency']["value"] : "One-Time",
                    ];
                } else {
                    $projectControl = ProjectControl::find($eachValue["controlId"]);
                    $projectControl->update([
                        'applicable' => (is_null($eachValue['checked']) || $eachValue['checked'] == true) ? 1 : 0,
                    ]);
                }
                $projectControlIds[] = $eachValue["controlId"];
            }
        }

        // //filtering out controls to be created or updated
        // foreach ($input['project_control_id'] as $index => $value) {
        //     if (!is_null($input['responsible'][$index]) && !is_null($input['approver'][$index])) {
        //         $inputsPerRows[] = [
        //             'project_control_id' => $value,
        //             'applicable' => $input['applicable'][$index],
        //             'responsible' => $input['responsible'][$index],
        //             'approver' => $input['approver'][$index],
        //             'deadline' => $input['deadline'][$index],
        //             'frequency' => $input['frequency'][$index],
        //         ];
        //     }

        //     // Update applicable status for unassign users
        //     else {
        //         $projectControl = ProjectControl::find($value);
        //         $projectControl->update([
        //             'applicable' => $input['applicable'][$index],
        //         ]);
        //     }
        // }

        // Handling the controls un-assignment
        $alreadyAssignedUsers = ProjectControl::where('project_id', $project->id)->where('status', 'Not Implemented')->whereIn('id', $projectControlIds)->whereNotNull('approver')->whereNotNull('responsible')->get();

        /* Filtering out controls to be un-assigned */
        $usersToBeUnassigned = $alreadyAssignedUsers->filter(function ($item) use ($inputsPerRows) {
            return !in_array($item->id, array_column($inputsPerRows, 'project_control_id'));
        });

        //Controls to be sent mail for assignment
        $controlsToBeSentAssignmentMail = collect([]);

        //Handling new assignment or change assignment
        foreach ($inputsPerRows as $key => $inputsPerRow) {
            $projectControl = ProjectControl::find($inputsPerRow['project_control_id']);

            if ($projectControl) {
                // checking control is editable
                if ($projectControl->is_editable) {
                    if (!$inputsPerRow['applicable']) {
                        $projectControl->update([
                            'applicable' => $inputsPerRow['applicable'],
                        ]);
                    } else {
                        // checking Approver and Responsibe are not same
                        if ((!is_null($inputsPerRow['responsible']) && !is_null($inputsPerRow['approver'])) && ($inputsPerRow['responsible'] != $inputsPerRow['approver'])) {
                            // Project controls before update

                            // $currentDate = (new \DateTime())->format('Y-m-d');
                            // $isValidDeadline = $inputsPerRow['deadline'] >= $currentDate;
                            $isValidDeadline = true;

                            // First time assignment of responsible and approver to control
                            if (is_null($projectControl->responsible) && is_null($projectControl->approver)) {
                                if ($isValidDeadline) {
                                    $projectControl->applicable = $inputsPerRow['applicable'];
                                    $projectControl->responsible = $inputsPerRow['responsible'];
                                    $projectControl->approver = $inputsPerRow['approver'];
                                    $projectControl->deadline = $inputsPerRow['deadline'];
                                    $projectControl->frequency = $inputsPerRow['frequency'];
                                    $projectControl->update();

                                    // Sending email on task assignment
                                    $projectControl['sent_to_approver'] = true;
                                    $projectControl['sent_to_responsible'] = true;
                                    $controlsToBeSentAssignmentMail->push($projectControl);
                                }
                            } else {
                                /* Handling assignment change */
                                $responsibleAssignmentChanged = $inputsPerRow['responsible'] != $projectControl->responsible ? true : false;
                                $approvalAssignmentChanged = $inputsPerRow['approver'] != $projectControl->approver ? true : false;

                                $projectControl->applicable = $inputsPerRow['applicable'];

                                // Sending control un-assignment mail to old responsible and approver
                                if ($responsibleAssignmentChanged || $approvalAssignmentChanged) {
                                    $projectControl['sent_to_responsible'] = $responsibleAssignmentChanged;
                                    $projectControl['sent_to_approver'] = $approvalAssignmentChanged;
                                    $usersToBeUnassigned->push($projectControl->replicate());
                                    unset($projectControl['sent_to_responsible']);
                                    unset($projectControl['sent_to_approver']);
                                }

                                if ($responsibleAssignmentChanged) {
                                    $projectControl->responsible = $inputsPerRow['responsible'];
                                }

                                if ($approvalAssignmentChanged) {
                                    $projectControl->approver = $inputsPerRow['approver'];
                                }

                                if ($isValidDeadline) {
                                    $projectControl->deadline = $inputsPerRow['deadline'];
                                }

                                //delete the compliance schedule record if user change frequency second time
                                if ($projectControl->frequency != $inputsPerRow['frequency']) {
                                    ComplianceProjectTaskScheduleRecord::where('compliance_project_control_id', $projectControl->id)->delete();
                                }
                                $projectControl->frequency = $inputsPerRow['frequency'];

                                $projectControl->update();

                                // Sending email to newly assigned users
                                if ($responsibleAssignmentChanged || $approvalAssignmentChanged) {
                                    $projectControl['sent_to_approver'] = $approvalAssignmentChanged;
                                    $projectControl['sent_to_responsible'] = $responsibleAssignmentChanged;
                                    $controlsToBeSentAssignmentMail->push($projectControl->replicate());
                                }
                            }
                        }
                    }
                }
            }
        }

        //Sending mail for first time assignment
        if (count($controlsToBeSentAssignmentMail) > 0) {
            $this->sendControlsAssignmentMail($controlsToBeSentAssignmentMail, $project);
        }

        //getting all un-assigned approver and responsible users
        if (count($usersToBeUnassigned) > 0) {
            /* Getting unique control by responsible and approvar */
            $uniqueResponsibleProjectControls = $usersToBeUnassigned->unique('responsible');
            $uniqueApproverProjectControls = $usersToBeUnassigned->unique('approver');

            /* Sending responsible users mail notification of un-assigment */
            foreach ($uniqueResponsibleProjectControls as $uniqueResponsibleProjectControl) {
                $cols = $usersToBeUnassigned->where('responsible', $uniqueResponsibleProjectControl->responsible);

                //filtering out the responsible user has changed
                $controls = $cols->filter(function ($item) {
                    return isset($item->sent_to_responsible) ? ($item->sent_to_responsible == true ? true : false) : true;
                });

                if (count($controls) > 0) {
                    $subject = 'Removal of task assignment';
                    $data = [
                        'greeting' => 'Hello ' . decodeHTMLEntity($uniqueResponsibleProjectControl->responsibleUser->full_name),
                        'title' => 'You have been removed responsibility for providing evidence for the following tasks:',
                        'project' => $project,
                        'projectControls' => $controls,
                        'information' => "This is an informational email and you don't have to take any action.",
                    ];

                    try {
                        // Mail::to($uniqueResponsibleProjectControl->responsibleUser->email)->send(new ControlAssignmentRemoval($data, $subject));
                    } catch (\Throwable $th) {
                        return redirect()->back()->withError("Failed to process request. Please check SMTP authentication connection sec.");
                    }
                }
            }

            /* Sending approver users mail notification of un-assigment */
            foreach ($uniqueApproverProjectControls as $uniqueApproverProjectControl) {
                $cols = $usersToBeUnassigned->where('approver', $uniqueApproverProjectControl->approver);
                //filtering out the approver user has changed
                $controls = $cols->filter(function ($item) {
                    return isset($item->sent_to_approver) ? ($item->sent_to_approver == true ? true : false) : true;
                });
                if (count($controls) > 0) {
                    $subject = 'Removal of approval responsibility';
                    $data = [
                        'greeting' => 'Hello ' . decodeHTMLEntity($uniqueApproverProjectControl->approverUser->full_name),
                        'title' => 'You have been removed as an approver for the following tasks:',
                        'project' => $project,
                        'projectControls' => $controls,
                        'information' => "This is an informational email and you don't have to take any action.",
                    ];

                    try {
                        // Mail::to($uniqueApproverProjectControl->approverUser->email)->send(new ControlAssignmentRemoval($data, $subject));
                    } catch (\Throwable $th) {
                        return redirect()->back()->withError("Failed to process request. Please check SMTP authentication connection fir.");
                    }
                }
            }

            // Resetting the control value after unassignment
            foreach ($usersToBeUnassigned as $projectControl) {
                $currentDate = (new \DateTime())->format('Y-m-d');
                $projectControl->responsible = null;
                $projectControl->approver = null;
                $projectControl->deadline = null;
                $projectControl->frequency = null;
                $projectControl->update();
            }
        }

        return redirect()->back()->withSuccess("Controls updated successfully");
    }

    /*
    |--------------------------------------------------------------------------
    | update controls
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Project $project, $projectControlId)
    {
        if (!Auth::guard('admin')->user()->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            return RegularFunctions::accessDeniedResponse();
        }

        $request->validate([
            'responsible' => 'required|different:approver',
            'approver' => 'required|different:responsible',
            'deadline' => 'required',
            'frequency' => 'required|in:One-Time,Monthly,Every 3 Months,Bi-Annually,Annually',
        ]);

        $input = $request->only('responsible', 'approver', 'deadline', 'frequency');

        $currentDate = (new \DateTime())->format('Y-m-d');
        $isValidDeadline = $input['deadline'] >= $currentDate;

        // Not allowing the Deadline to be less than today
        if (!$isValidDeadline) {
            unset($input['deadline']); // remove item deadline
        }

        //Not allowing the responsible and approver to be same
        if ($input['responsible'] == $input['approver']) {
            unset($input['responsible']); // remove item index responsible
            unset($input['approver']); // remove item index approver
        }

        // Update not allowed for non applicable
        $isNotApplicable = ProjectControl::where('id', $projectControlId)->where('applicable', 0)->first();

        if ($isNotApplicable) {
            return redirect()->back();
        }

        // Update not allowed for non editable
        $isNotEditable = ProjectControl::where('id', $projectControlId)->where('is_editable', 0)->first();

        if ($isNotEditable) {
            return redirect()->back();
        }

        $projectControl = ProjectControl::where('id', $projectControlId)->first();

        // project control before update
        $beforeUpdateProjectControl = $projectControl->toArray();

        // updating project Control
        $projectControl->update($input);

        // Sending email to responsible when responsible user changed
        if (array_key_exists('responsible', $input) && !is_null($input['responsible']) && $input['responsible'] != $beforeUpdateProjectControl['responsible']) {

            $user = Admin::find($projectControl->responsible);

            $subject = 'New task assignment';

            $data = [
                'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($user->full_name)),
                'content1' => 'You have been assigned responsibility for a new task. Please find the details below:',
                'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                'content6' => '<b style="color: #000000;">Deadline: </b> ' . date('j M Y', strtotime($projectControl->deadline)),
                'content7' => '',
                'action' => [
                    'action_title' => '',
                    'action_url' => route('compliance-dashboard'),
                    'action_button_text' => 'Go to my dashboard',
                ],
            ];

            Notification::route('mail', $user->email)
                ->notify(new AssignedTaskNotification($data, $subject));

            $admin = Admin::find($beforeUpdateProjectControl['responsible']);

            $subjects = 'Removal of task assignment';

            if ($admin) {
                $removaldata = [
                    'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($admin->full_name)),
                    'content1' => 'You have been removed responsibility for providing evidence for the following tasks:',
                    'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                    'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                    'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                    'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                    'content6' => '',
                    'content7' => "This is an informational email and you don't have to take any action.",
                ];

                Notification::route('mail', $admin->email)
                    ->notify(new RemoveTaskNotification($removaldata, $subjects));
            }
        }

        // Sending email to approver when approver user changed
        if (array_key_exists('approver', $input) && !is_null($input['approver']) && $input['approver'] != $beforeUpdateProjectControl['approver']) {
            $user = Admin::find($projectControl->approver);

            $subject = 'Assignment as control approver';
            $data = [
                'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($user->full_name)),
                'content1' => 'You have been assigned as an approver for a new task. Please find the details below:',
                'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                'content6' => '<b style="color: #000000;">Deadline: </b> ' . date('j M Y', strtotime($projectControl->deadline)),
                'content7' => "You don't have to take any action now. You'll get another email when your approval is required.",
            ];

            Notification::route('mail', $user->email)
                ->notify(new AssignedTaskNotification($data, $subject));

            $admin = Admin::find($beforeUpdateProjectControl['approver']);

            $subjects = 'Removal of approval responsibility';

            if ($admin) {
                $removaldata = [
                    'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($admin->full_name)),
                    'content1' => 'You have been removed as an approver from the following tasks:',
                    'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                    'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                    'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                    'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                    'content6' => '',
                    'content7' => "This is an informational email and you don't have to take any action.",
                ];

                Notification::route('mail', $admin->email)
                    ->notify(new RemoveTaskNotification($removaldata, $subjects));
            }
        }

        return redirect()->back()->withSuccess('Control Detail is successfully updated');
    }

    /*
    |--------------------------------------------------------------------------
    | Sending Task first time assignment mail
    |--------------------------------------------------------------------------
    */
    public function sendControlsAssignmentMail($projectControls, $project)
    {
        $uniqueResponsibleUsers = $projectControls->unique('responsible');
        $uniqueApproverUsers = $projectControls->unique('approver');

        /* Sending responsible users mail notification of un-assigment */
        if ($uniqueResponsibleUsers) {
            foreach ($uniqueResponsibleUsers as $uniqueResponsibleProjectControl) {
                /* Filtering out controls to assign  */
                $cols = $projectControls->where('responsible', $uniqueResponsibleProjectControl->responsible);

                $controls = $cols->filter(function ($item) {
                    return $item->sent_to_responsible == true;
                });

                if (count($controls) > 0) {
                    $subject = 'New task assignment';
                    $data = [
                        'greeting' => 'Hello ' . decodeHTMLEntity($uniqueResponsibleProjectControl->responsibleUser->full_name),
                        'title' => 'You have been assigned responsibility for a new task. Please find the details below:',
                        'project' => $project,
                        'projectControls' => $controls,
                        'information' => '',
                        'action' => [
                            'action_title' => '',
                            'action_url' => route('compliance-dashboard'),
                            'action_button_text' => 'Go to my dashboard',
                        ],
                    ];
                    Mail::to($uniqueResponsibleProjectControl->responsibleUser->email)->send(new AssignTaskEmail($data, $subject));
                }
            }
        }

        /* Sending approver users mail notification of un-assigment */
        if ($uniqueApproverUsers) {
            foreach ($uniqueApproverUsers as $uniqueApproverProjectControl) {
                /* Filtering out controls to be un-assigned */
                $cols = $projectControls->where('approver', $uniqueApproverProjectControl->approver);

                $controls = $cols->filter(function ($item) {
                    return $item->sent_to_approver == true;
                });
                if (count($controls) > 0) {
                    $subject = 'Assignment as control approver';
                    $data = [
                        'greeting' => 'Hello ' . decodeHTMLEntity($uniqueApproverProjectControl->approverUser->full_name),
                        'title' => 'You have been assigned as an approver for a new task. Please find the details below:',
                        'project' => $project,
                        'projectControls' => $controls,
                        'information' => "You don't have to take any action now. You'll get another email when your approval is required.",
                    ];

                    Mail::to($uniqueApproverProjectControl->approverUser->email)->send(new AssignTaskEmail($data, $subject));
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | uploading control evidences
    |--------------------------------------------------------------------------
    */
    public function uploadEvidences(Request $request, Project $project, ProjectControl $projectControl)
    {
        switch ($request->input('active_tab')) {
            case "upload-docs":
                $this->validate($request, [
                    'project_control_id' => 'required',
                    'name2' => 'required|string|max:191',
                    'evidences' => ['required', 'max:10240', new AllowedEvidence()],
                ], [
                    'name2.required' => 'The name field is required',
                    'evidences.max' => 'The upload max filesize is 10MB. Please upload file less than 10MB.'
                ]);
                break;
            case 'create-link':
                $this->validate($request, [
                    'project_control_id' => 'required',
                    'name' => 'required|string|max:191',
                    'link' => [
                        'required',
                        'string',
                        'max:191',
                        new ValidateUrlOrNetworkFolder
                    ]
                ]);
                break;
            case 'existing-control':
                $this->validate($request, [
                    'project_control_id' => 'required',
                    'linked_to_project_control_id' => 'required'
                ], [
                    'linked_to_project_control_id.required' => 'Please select a control first.'
                ]);
                break;
            case 'text-input':
                $this->validate($request, [
                    'project_control_id' => 'required',
                    'text_evidence' => 'required|string',
                    'text_evidence_name' => 'required|string'
                ], [
                    'text_evidence.required' => 'Text field is required',
                    'text_evidence_name.required' => 'Name field is required'
                ]);
        }

        $input = $request->toArray();

        if ($projectControl) {
            $projectControlId = $projectControl->id;

            if ($projectControl->responsible != $this->loggedUser->id) {
                exit;
            }

            // evidences as documents
            if ($request->hasFile('evidences') && $request->name2) {
                $document = $request->file('evidences');
                $fileName = $document->getClientOriginalName();
                $uploadedDocument = Evidence::create([
                    'project_control_id' => $projectControlId,
                    'name' => $input['name'] ?: $input['name2'],
                    'path' => $fileName,
                    'type' => 'document',
                    'deadline' => $projectControl->deadline,
                    'status' => 'initial'
                ]);

                $filePath = "private/compliance/evidences/{$uploadedDocument->id}/{$fileName}";
                // Get File Content
                $documentContent = $document->get();
                // Encrypt the Content
                $encryptedContent = encrypt($documentContent);
                // Store the encrypted Content
                Storage::put($filePath, $encryptedContent, 'private');

                $uploadedDocument->update([
                    'path' => $filePath,
                ]);
                $evidence = $uploadedDocument;
            }

            // Evidences as link
            if ($request->link && $request->name) {
                $evidence = Evidence::create([
                    'project_control_id' => $projectControlId,
                    'name' => $input['name'] ?: $input['name2'],
                    'path' => $input['link'],
                    'type' => 'link',
                    'deadline' => $projectControl->deadline,
                    'status' => 'initial'
                ]);
            }

            // evidences as existing controls
            if (!is_null($request->linked_to_project_control_id)) {
                $linkedToProjectControl = ProjectControl::find($request->linked_to_project_control_id);

                if ($linkedToProjectControl) {
                    $evidence = Evidence::create([
                        'project_control_id' => $projectControlId,
                        'name' => $linkedToProjectControl->name,
                        'path' => $linkedToProjectControl->id,
                        'type' => 'control',
                        'deadline' => $projectControl->deadline,
                        'status' => 'initial'
                    ]);
                }
            }

            // evidences as text
            if ($request->text_evidence) {
                $evidence = Evidence::create([
                    'project_control_id' => $projectControlId,
                    'name' => $input['text_evidence_name'],
                    'text_evidence' => $input['text_evidence'],
                    'path' => "text evidence",
                    'type' => 'text',
                    'deadline' => $projectControl->deadline,
                    'status' => 'initial'
                ]);
            }

            /* Rejected evidences are deleted when new evidece(s) are uploaded */
            if ($projectControl->isEligibleForReview) {
                $rejectedEvidences = Evidence::where('project_control_id', $projectControl->id)->where('status', 'rejected')->get();

                foreach ($rejectedEvidences as $key => $rejectedEvidence) {
                    if ($rejectedEvidence->type == 'document') {
                        $exists = Storage::exists($rejectedEvidence->path);

                        if ($exists) {
                            Storage::deleteDirectory(dirname($rejectedEvidence->path));
                        }
                    }

                    /* Deleting the evidence(s) record from DB*/
                    $rejectedEvidence->delete();
                }
            }
        }

        return redirect()->back()->withSuccess('Evidence successfully uploaded');
    }

    /*
    |--------------------------------------------------------------------------
    | delete evidences
    |--------------------------------------------------------------------------
    */
    public function deleteEvidences(Request $request, $project, $projectControl, $id)
    {
        $evidence = Evidence::withoutGlobalScope(DataScope::class)->findorfail($id);
        $projectControlData = ProjectControl::withoutGlobalScope(DataScope::class)->findorfail($projectControl);
        if ($projectControlData->responsible != $this->loggedUser->id) {
            return RegularFunctions::accessDeniedResponse();
        } else {
            if ($projectControlData->status == 'Approved' || $projectControlData->status == 'Under Review') {
                return RegularFunctions::accessDeniedResponse();
            }
        }

        if ($evidence->type == 'document') {
            $exists = Storage::exists($evidence->path);

            if ($exists) {
                $unlinked = Storage::deleteDirectory(dirname($evidence->path));
            }
        }

        // Deleting unlinked evidence from database
        $evidence->delete();

        return response()->json(['success' => true, 'message' => 'Evidence deleted successfully!']);
    }

    /*
    |--------------------------------------------------------------------------
    | approving controls
    |--------------------------------------------------------------------------
    */
    public function controlReviewApprove(Request $request, Project $project, ProjectControl $projectControl,$directImplement=false)
    {
        if (!$projectControl) {
            exit;
        }

        if ($projectControl->approver != $this->loggedUser->id && !$directImplement) {
            exit;
        }

        $subject = 'Approval of submitted evidence';

        $responsible = Admin::findorfail($projectControl->responsible);

        try {
            DB::beginTransaction();
            if ($projectControl->amend_status == "accepted" || $projectControl->amend_status == "submitted") {
                $projectControl->amend_status = "solved";
            }
            $projectControl->status = 'Implemented';
            $projectControl->current_cycle = $projectControl->current_cycle + 1;
            $projectControl->approved_at = date('Y-m-d H:i:s');
            $projectControl->update();

            /* Changing the evidence(s) status */
            Evidence::where('project_control_id', $projectControl->id)->update([
                'status' => 'approved'
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Oops something went wrong',
            ]);
        }


        $data = [
            'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($responsible->full_name)),
            'content1' => 'The evidence you have uploaded for an assigned task has been approved. Please find the details below:',
            'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
            'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
            'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
            'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
            'content6' => '<b style="color: #000000;">Status: </b> Approved',
            'content7' => 'No further action is needed.',
        ];

        Notification::route('mail', $responsible->email)
            ->notify(new AssignedTaskNotification($data, $subject));

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | reject controls
    |--------------------------------------------------------------------------
    */
    public function controlReviewReject(Request $request, Project $project, ProjectControl $projectControl)
    {
        $request->validate([
            'justification' => 'required',
        ]);

        if ($projectControl->approver != $this->loggedUser->id) {
            return redirect()->back()->withError("You aren't an approver");
        }

        $responsible = Admin::findorfail($projectControl->responsible);

        try {
            DB::beginTransaction();
            if (in_array($projectControl->amend_status, ['accepted', 'submitted'])) {
                $projectControl->amend_status = "rejected";
            }

            $projectControl->status = 'Rejected';
            $projectControl->is_editable = 1;
            $projectControl->rejected_at = date('Y-m-d H:i:s');
            $projectControl->save();

            /* Changing the evidence(s) status */
            Evidence::where('project_control_id', $projectControl->id)->where('status', 'review')->update([
                'status' => 'rejected'
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Oops something went wrong',
            ]);
        }


        // update evidence upload
        RegularFunctions::updateTaskEvidencesUploadAllowedStatus($projectControl->id, 1);

        // creating for evidences rejection
        $justification = Justification::create([
            'project_control_id' => $projectControl->id,
            'justification' => $request->justification,
            'for' => 'rejected',
            'creator_id' => $this->loggedUser->id,
        ]);

        $subject = 'Rejection of submitted evidence';

        $data = [
            'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($responsible->full_name)),
            'content1' => 'The evidence you have uploaded for an assigned task has been rejected. Please find the details below:',
            'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
            'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
            'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
            'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
            'content6' => '<b style="color: #000000;">Rejection Reason: </b> ' . decodeHTMLEntity($justification->justification),
            'content7' => '',
            'email' => $responsible->email,
            'action' => [
                'action_title' => 'Click the below button to re-upload new evidence.',
                'action_url' => route('compliance-project-control-show', [$project->id, $projectControl->id, 'tasks']),
                'action_button_text' => 'Go to task details',
            ],
        ];

        Notification::route('mail', $responsible->email)
            ->notify(new AssignedTaskNotification($data, $subject));

        return redirect()->back();
    }

    public function requestEvidenceAmendment(Request $request, $project, $projectControl)
    {
        $request->validate([
            'justification' => 'required',
        ]);

        $project = Project::withoutGlobalScope(DataScope::class)->find($project);
        $projectControl = ProjectControl::withoutGlobalScope(DataScope::class)->find($projectControl);

        $projectControl->amend_status = $request->requested_by === "responsible" ? "requested_responsible" : "requested_approver";

        if($request->requested_by === "approver") {
            $projectControl->status = "Not Implemented";
        }
        $projectControl->save();

        $justification = Justification::create([
            'project_control_id' => $projectControl->id,
            'justification' => $request->justification,
            'for' => 'amend',
            'creator_id' => $this->loggedUser->id,
        ]);

        $subject = "Request for amendment of evidence";


        if($request->requested_by === "responsible"){
            $user_to_be_notified = $projectControl->approverUser;

            $email_data = [
                'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($user_to_be_notified->full_name)),
                'content1' => "The responsible person for the below task has requested to amend the previously provided evidence. Please find the details below: ",
                'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                'content6' => '<b style="color: #000000;">Amendment Reason: </b> ' . decodeHTMLEntity($request->justification),
                'content7' => "",
                'action' => [
                    'action_title' => 'Click the below button to view the request and to carry out the approval.',
                    'action_url' => route('compliance-project-control-show', [$project->id, $projectControl->id, 'tasks']),
                    'action_button_text' => 'Go to task details',
                ]
            ];
        } else {
            $user_to_be_notified = $projectControl->responsibleUser;
            $email_data = [
                'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($user_to_be_notified->full_name)),
                'content1' => "You have been requested to amend the evidence you have uploaded for an assigned task. Please find the details below:",
                'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                'content6' => '<b style="color: #000000;">Amendment Reason: </b> ' . decodeHTMLEntity($request->justification),
                'content7' => "",
                'action' => [
                    'action_title' => 'Click the below button to re-upload new evidence.',
                    'action_url' => route('compliance-project-control-show', [$project->id, $projectControl->id, 'tasks']),
                    'action_button_text' => 'Go to task details',
                ]
            ];
        }

        Notification::route('mail', $user_to_be_notified->email)->notify(new AssignedTaskNotification($email_data, $subject));

        Log::info('Evidence amendment was requested');
        return redirect()->back();
    }

    public function amendRequestDecision(Request $request, $project, $projectControl)
    {

        $project = Project::withoutGlobalScope(DataScope::class)->find($project);
        $projectControl = ProjectControl::withoutGlobalScope(DataScope::class)->find($projectControl);

        if ($projectControl->approver != $this->loggedUser->id) {
            return response()->json([
                'status' => 'access denied',
                'message' => 'your are not approver',
                'justification' => 'sometimes|string'
            ]);
        }

        $user_to_be_notified = $projectControl->responsibleUser;
        $request_justification = Justification::where('project_control_id', $projectControl->id)->where('for', 'amend')->latest('created_at')->first();


        if($request->solution === "accepted") {

            try {
                DB::beginTransaction();

                $projectControl->amend_status = $request->solution;
                $projectControl->is_editable = $request->solution === "accepted" ? 1 : 0;
                $projectControl->status = "Not Implemented";
                $saved = $projectControl->save();

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Oops something went wrong',
                ]);
            }

            RegularFunctions::updateTaskEvidencesUploadAllowedStatus($projectControl->id, 1);

            $subject = "Approval of amending evidence";

            $email_data = [
                'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($user_to_be_notified->full_name)),
                'content1' => "Your request for amending evidence has been approved. Please find the details below:",
                'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                'content6' => '<b style="color: #000000;">Amendment Reason: </b> ' . decodeHTMLEntity($request_justification->justification),
                'content7' => "",
                'action' => [
                    'action_title' => 'Click the below button to upload amended evidence.',
                    'action_url' => route('compliance-project-control-show', [$project->id, $projectControl->id, 'tasks']),
                    'action_button_text' => 'Go to task details',
                ]
            ];
        } else {
            if($request->justification){
                $reject_justification = Justification::create([
                    'project_control_id' => $projectControl->id,
                    'justification' => $request->justification,
                    'for' => 'amend_reject',
                    'creator_id' => $this->loggedUser->id,
                ]);
            }

            try {
                DB::beginTransaction();

                $projectControl->amend_status = "solved";

                $saved = $projectControl->save();

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Oops something went wrong',
                ]);
            }

            $subject = "Rejection of amending evidence";

            $email_data = [
                'greeting' => 'Hello ' . ucwords(decodeHTMLEntity($user_to_be_notified->full_name)),
                'content1' => "Your request for amending evidence has been rejected. Please find the details below:",
                'content2' => '<b style="color: #000000;">Project Name: </b> ' . decodeHTMLEntity($project->name),
                'content3' => '<b style="color: #000000;">Standard: </b> ' . decodeHTMLEntity($project->standard),
                'content4' => '<b style="color: #000000;">Control ID: </b> ' . decodeHTMLEntity($projectControl->controlId),
                'content5' => '<b style="color: #000000;">Control Name: </b> ' . decodeHTMLEntity($projectControl->name),
                'content6' => '<b style="color: #000000;">Amendment Reason: </b> ' . decodeHTMLEntity($request_justification->justification),
                'content7' => "",
                'content8' => '<b style="color: #000000;">Rejection Reason: </b> ' . decodeHTMLEntity($reject_justification ? $reject_justification->justification  : ""),
                'action' => [
                    'action_title' => 'Click the below button to go to the task.',
                    'action_url' => route('compliance-project-control-show', [$project->id, $projectControl->id, 'tasks']),
                    'action_button_text' => 'Go to task details',
                ]
            ];
        }

        Notification::route('mail', $user_to_be_notified->email)->notify(new AssignedTaskNotification($email_data, $subject));

        Log::info("Evidence amendment requested was $request->solution");

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Link control evidence show
    |--------------------------------------------------------------------------
    */
    public function linkedControlEvidencesView(Request $request, Project $project, $projectControlId, $linkedToControlId)
    {
        $evidences = Evidence::where('project_control_id', $projectControlId)->get();

        return inertia('controls/LinkedControlEvidences', compact(
            'project',
            'projectControlId',
            'linkedToControlId',
            'evidences'
        ));
        return view('compliance.projects.linked-control-evidences', compact(
            'project',
            'projectControlId',
            'linkedToControlId',
            'evidences'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Link control evidence
    |--------------------------------------------------------------------------
    */
    public function linkedControlEvidences(Request $request, Project $project, $projectControlId, $linkedToControlId)
    {
        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;
        $page = $request->page ?? 1;
        $size = $request->per_page ?? 10;
        $count = [];
        $render = [];

        $projectControl = projectControl::find($projectControlId);

        $evidences = Evidence::where('project_control_id', $projectControl->id)->skip(--$page * $size)->take($size)->paginate($size);

        foreach ($evidences as $evidence) {
            $evidence['created_date'] = date('d M, Y', strtotime($evidence->created_at));
            $evidence['deadline'] = date('d M, Y', strtotime($evidence->created_at));
        }
        return response()->json([
            'data' => $evidences,
            'total' => $evidences->count(),
        ], 200);

        foreach ($evidences as $evidence) {
            $evidenceType = $evidence->type;

            $evidenceName = $evidence->name;
            $urlLink = "<a class='btn btn-secondary btn-xs waves-effect waves-light' title='Download' href='" . route('compliance-project-control-evidences-download', [$project->id, $evidence->project_control_id, $evidence->id]) . "'><i class='fe-download' style='font-size:20px;'></i></a>";

            switch ($evidenceType) {
                case 'control':
                    $evidenceName = 'This control is linked to <a href=' . route('project-control-linked-controls-evidences-view', [$project->id, $evidence->path, $evidence->project_control_id]) . ">{$evidence->name}
                                </a>
                                ";
                    $urlLink = "<a href='" . route('project-control-linked-controls-evidences-view', [$project->id, $evidence->path, $evidence->project_control_id]) . "'><i class='fe-link' style='font-size:20px;'></i></a>";
                    break;
                case 'link':
                    $urlLink = "<a href='" . $evidence->path . "' target='_blank'><i class='fe-link' style='font-size:20px;'></i></a>";
                    break;
            }

            $render[] = [
                $evidenceName,
                ucfirst($evidenceType),
                date('j M Y', strtotime($evidence->deadline)),
                date('j M Y', strtotime($evidence->created_at)),
                $urlLink,
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => count($evidences),
            'recordsFiltered' => count($evidences),
            'data' => $render,
        ];

        return response()->json($response);
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Link controll evidence
    |--------------------------------------------------------------------------
    */

    public function removeLinkedControls(Request $request, Project $project, $projectControlId)
    {
        $foundModel = EvidencesLinkedProjectControl::where('project_control_id', $projectControlId)->first();

        if ($foundModel) {
            $evidence = Evidence::where('project_control_id', $projectControlId)->first();

            $foundModel->delete();
        }

        return response()->json([
            'success' => true,
        ]);
    }


    /**
     * Project Controls.
     **/
    public function ControlsJson(Request $request, Project $project)
    {
        $page = $request->page ?? 1;
        $per_page = $request->per_page ?? 10;
        $keyword = $request->search ?? null;


        // filtring control for only loging user
        if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            $projectControlsQuery = $project->controls()->where(function ($query) {
                $query->where('approver', $this->loggedUser->id)
                    ->orWhere('responsible', $this->loggedUser->id);
            });

            if ($keyword) {
                $projectControlsQuery->where(function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere(DB::raw("CONCAT_WS(id_separator, primary_id, sub_id)"), 'LIKE', "%{$keyword}%")
                        ->orWhereHas('responsibleUser', function ($q) use ($keyword) {
                            $q->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', "%{$keyword}%");
                        })
                        ->orWhereHas('approverUser', function ($q) use ($keyword) {
                            $q->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', "%{$keyword}%");
                        });
                });
            }

            $count = $projectControlsQuery->count();
            $controls = $projectControlsQuery->skip(--$page * $per_page)->take($per_page)->paginate($per_page);
        } else {
            $projectControlsQuery = $keyword ?
                $project->controls()
                    ->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere(DB::raw("CONCAT_WS(id_separator, primary_id, sub_id)"), 'LIKE', "%{$keyword}%")
                    ->orWhereHas('responsibleUser', function ($q) use ($keyword) {
                        $q->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', "%{$keyword}%");
                    })
                    ->orWhereHas('approverUser', function ($q) use ($keyword) {
                        $q->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', "%{$keyword}%");
                    })
                : $project->controls();

            $count = $projectControlsQuery->count();
            $controls = $projectControlsQuery->skip(--$page * $per_page)->take($per_page)->paginate($per_page);
        }


        return response()->json([
            'total' => $count,
            'data' => $controls
        ]);
    }

    public function updateAllJson(Request $request, Project $project)
    {
        if (!Auth::guard('admin')->user()->hasAnyRole(['Global Admin', 'Compliance Administrator'])) {
            return RegularFunctions::accessDeniedResponse();
        }

        $input = $request->controls;
        $projectControlIds = [];
        $inputsPerRows = [];

        if (count($input) > 0) {
            foreach ($input as $eachValue) {
                if (!is_null($eachValue['responsible']) && !is_null($eachValue['approver'])) {
                    $inputsPerRows[] = [
                        'project_control_id' => $eachValue["id"],
                        'applicable' => $eachValue["applicable"],
                        'responsible' => $eachValue['responsible'],
                        'approver' => $eachValue['approver'],
                        'deadline' => $eachValue['deadline'] != null ? $eachValue['deadline'] : date("Y-m-d"),
                        'frequency' => $eachValue['frequency'] != null ? $eachValue['frequency'] : "One-Time",
                    ];
                } else {
                    $projectControl = ProjectControl::find($eachValue["id"]);
                    $projectControl->update([
                        'applicable' => $eachValue["applicable"],
                    ]);
                }
                $projectControlIds[] = $eachValue["id"];
            }
        }

        // Handling the controls un-assignment
        $alreadyAssignedUsers = ProjectControl::where('project_id', $project->id)->where('status', 'Not Implemented')->whereIn('id', $projectControlIds)->whereNotNull('approver')->whereNotNull('responsible')->get();

        /* Filtering out controls to be un-assigned */
        $usersToBeUnassigned = $alreadyAssignedUsers->filter(function ($item) use ($inputsPerRows) {
            return !in_array($item->id, array_column($inputsPerRows, 'project_control_id'));
        });

        //Controls to be sent mail for assignment
        $controlsToBeSentAssignmentMail = collect([]);

        //Handling new assignment or change assignment
        foreach ($inputsPerRows as $key => $inputsPerRow) {
            $projectControl = ProjectControl::find($inputsPerRow['project_control_id']);
            if ($projectControl) {
                // checking control is editable
                if ($projectControl->is_editable) {
                    if (!$inputsPerRow['applicable']) {
                        $projectControl->update([
                            'applicable' => $inputsPerRow['applicable'],
                        ]);
                    } else {
                        // checking Approver and Responsibe are not same
                        if ((!is_null($inputsPerRow['responsible']) && !is_null($inputsPerRow['approver'])) && ($inputsPerRow['responsible'] != $inputsPerRow['approver'])) {
                            // Project controls before update
                            // $currentDate = (new \DateTime())->format('Y-m-d');
                            // $isValidDeadline = $inputsPerRow['deadline'] >= $currentDate;
                            $isValidDeadline = true;

                            // First time assignment of responsible and approver to control
                            if (is_null($projectControl->responsible) && is_null($projectControl->approver)) {
                                if ($isValidDeadline) {
                                    $projectControl->applicable = $inputsPerRow['applicable'];
                                    $projectControl->responsible = $inputsPerRow['responsible'];
                                    $projectControl->approver = $inputsPerRow['approver'];
                                    $projectControl->deadline = $inputsPerRow['deadline'];
                                    $projectControl->frequency = $inputsPerRow['frequency'];
                                    $projectControl->update();

                                    // Sending email on task assignment
                                    $projectControl['sent_to_approver'] = true;
                                    $projectControl['sent_to_responsible'] = true;
                                    $controlsToBeSentAssignmentMail->push($projectControl);
                                }
                            } else {
                                /* Handling assignment change */
                                $responsibleAssignmentChanged = $inputsPerRow['responsible'] != $projectControl->responsible ? true : false;
                                $approvalAssignmentChanged = $inputsPerRow['approver'] != $projectControl->approver ? true : false;

                                $projectControl->applicable = $inputsPerRow['applicable'];

                                // Sending control un-assignment mail to old responsible and approver
                                if ($responsibleAssignmentChanged || $approvalAssignmentChanged) {
                                    $projectControl['sent_to_responsible'] = $responsibleAssignmentChanged;
                                    $projectControl['sent_to_approver'] = $approvalAssignmentChanged;
                                    $usersToBeUnassigned->push($projectControl->replicate());
                                    unset($projectControl['sent_to_responsible']);
                                    unset($projectControl['sent_to_approver']);
                                }

                                if ($responsibleAssignmentChanged) {
                                    $projectControl->responsible = $inputsPerRow['responsible'];
                                }

                                if ($approvalAssignmentChanged) {
                                    $projectControl->approver = $inputsPerRow['approver'];
                                }

                                if ($isValidDeadline) {
                                    $projectControl->deadline = $inputsPerRow['deadline'];
                                }

                                //delete the compliance schedule record if user change frequency second time
                                if ($projectControl->frequency != $inputsPerRow['frequency']) {
                                    ComplianceProjectTaskScheduleRecord::where('compliance_project_control_id', $projectControl->id)->delete();
                                }
                                $projectControl->frequency = $inputsPerRow['frequency'];

                                $projectControl->update();

                                // Sending email to newly assigned users
                                if ($responsibleAssignmentChanged || $approvalAssignmentChanged) {
                                    $projectControl['sent_to_approver'] = $approvalAssignmentChanged;
                                    $projectControl['sent_to_responsible'] = $responsibleAssignmentChanged;
                                    $controlsToBeSentAssignmentMail->push($projectControl->replicate());
                                }
                            }
                        }
                    }
                }
            }
        }

        //Sending mail for first time assignment
        if (count($controlsToBeSentAssignmentMail) > 0) {
            $this->sendControlsAssignmentMail($controlsToBeSentAssignmentMail, $project);
        }

        //getting all un-assigned approver and responsible users
        if (count($usersToBeUnassigned) > 0) {
            /* Getting unique control by responsible and approvar */
            $uniqueResponsibleProjectControls = $usersToBeUnassigned->unique('responsible');
            $uniqueApproverProjectControls = $usersToBeUnassigned->unique('approver');

            /* Sending responsible users mail notification of un-assigment */
            foreach ($uniqueResponsibleProjectControls as $uniqueResponsibleProjectControl) {
                $cols = $usersToBeUnassigned->where('responsible', $uniqueResponsibleProjectControl->responsible);

                //filtering out the responsible user has changed
                $controls = $cols->filter(function ($item) {
                    return isset($item->sent_to_responsible) ? ($item->sent_to_responsible == true ? true : false) : true;
                });

                if (count($controls) > 0) {
                    $subject = 'Removal of task assignment';
                    $data = [
                        'greeting' => 'Hello ' . decodeHTMLEntity($uniqueResponsibleProjectControl->responsibleUser->full_name),
                        'title' => 'You have been removed responsibility for providing evidence for the following tasks:',
                        'project' => $project,
                        'projectControls' => $controls,
                        'information' => "This is an informational email and you don't have to take any action.",
                    ];

                    try {
                        // Mail::to($uniqueResponsibleProjectControl->responsibleUser->email)->send(new ControlAssignmentRemoval($data, $subject));
                    } catch (\Throwable $th) {
                        return redirect()->back()->withError("Failed to process request. Please check SMTP authentication connection sec.");
                    }
                }
            }

            /* Sending approver users mail notification of un-assigment */
            foreach ($uniqueApproverProjectControls as $uniqueApproverProjectControl) {
                $cols = $usersToBeUnassigned->where('approver', $uniqueApproverProjectControl->approver);
                //filtering out the approver user has changed
                $controls = $cols->filter(function ($item) {
                    return isset($item->sent_to_approver) ? ($item->sent_to_approver == true ? true : false) : true;
                });
                if (count($controls) > 0) {
                    $subject = 'Removal of approval responsibility';
                    $data = [
                        'greeting' => 'Hello ' . decodeHTMLEntity($uniqueApproverProjectControl->approverUser->full_name),
                        'title' => 'You have been removed as an approver for the following tasks:',
                        'project' => $project,
                        'projectControls' => $controls,
                        'information' => "This is an informational email and you don't have to take any action.",
                    ];

                    try {
                        // Mail::to($uniqueApproverProjectControl->approverUser->email)->send(new ControlAssignmentRemoval($data, $subject));
                    } catch (\Throwable $th) {
                        return redirect()->back()->withError("Failed to process request. Please check SMTP authentication connection fir.");
                    }
                }
            }

            // Resetting the control value after unassignment
            foreach ($usersToBeUnassigned as $projectControl) {
                $currentDate = (new \DateTime())->format('Y-m-d');
                $projectControl->responsible = null;
                $projectControl->approver = null;
                $projectControl->deadline = null;
                $projectControl->frequency = null;
                $projectControl->update();
            }
        }

        return response()->json(['message' => 'Controls Updated Successfully']);
    }
}
