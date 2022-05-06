<?php

namespace App\Http\Controllers\Compliance\ImplementedControls;

use Zip;
use Auth;
use Illuminate\Http\Request;
use App\Utils\RegularFunctions;
use App\Models\Compliance\Project;
use App\Models\Compliance\Standard;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Compliance\ProjectControl;

class ReactImplementedControlsController extends Controller
{
    private $basePath = 'compliance.implemented-controls';
    private $loggedUser;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->loggedUser = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function index()
    {
        $taskContributors = RegularFunctions::getControlContributorList();
        $allStandards = Standard::whereHas('projects')->get();
        return view($this->basePath.'.index', compact('taskContributors', 'allStandards'));
    }

    public function getImplementedControlsData(Request $request)
    {
        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;

        $baseQuery = ProjectControl::where('status', 'Implemented')
            ->where(function ($query) use ($request) {
                if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor'])) {
                    $query->where('responsible', $this->loggedUser->id);
                }

                $controlName = $request->control_name;
                $controlID = $request->controlID;

                if ($controlName) {
                    $query->where('name', 'LIKE', '%'.$controlName.'%');
                }

                if ($controlID) {
                    $query->whereRaw("CONCAT(`primary_id`, `id_separator`, `sub_id`) LIKE ?", ['%'.$controlID.'%']);
                }
            })
            ->whereHas('responsibleUser', function ($query) use ($request) {
                $responsibleUser = $request->responsible_user;

                if ($this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor'])) {
                    if ($responsibleUser) {
                        $query->where('id', $responsibleUser);
                    }
                }
            })
            ->whereHas('project', function ($query) use ($request) {
                $standardId = $request->standard_id;
                $projectID = $request->project_id;

                if ($standardId) {
                    $query->where('standard_id', $standardId);
                }
                if ($projectID) {
                    $query->where('project_id', $projectID);
                }
            })

           ->with(['evidences']);
        $totalRecords = $baseQuery->count();

        $records = $baseQuery->get();

        $data = [];

        foreach ($records as $key => $record) {
            $actions = '';

            $linkEvidences = $record->evidences->where('type', 'link');
            $controlEvidences = $record->evidences->where('type', 'control');
            //cheaking if the evidences has link or control as a type: if so store each ones in array
            if ($linkEvidences->count() || $controlEvidences->count()) {
                $urls = [];

                if ($linkEvidences->count()) {
                    foreach ($linkEvidences as $linkEvidence) {
                        $urls[] = $linkEvidence->path;
                    }
                }

                if ($controlEvidences->count()) {
                    foreach ($controlEvidences as $controlEvidence) {
                        $urls[] = route('project-control-linked-controls-evidences-view', [$controlEvidence->projectControl->project_id, $controlEvidence->path, $controlEvidence->project_control_id]);
                    }
                }

                $actions .= "<a href='#' data-urls='".json_encode($urls)."' class='btn btn-secondary link-evidences-action btn-xs waves-effect waves-light' title='Link' class='link-evidences-action'><i class='fe-link' style='font-size:12px;'></i></a>";
            }
            //cheaking if the evidences has document as type
            if ($record->evidences->where('type', 'document')->count()) {
                $actions .= "<a class='btn btn-secondary btn-xs waves-effect waves-light' title='Download' href='".route('compliance.implemented-controls.download-evidences', $record->id)."'><i class='fe-download' style='font-size:12px;'></i></a>";
            }

            $latestEvidence = $record->evidences()->latest('id')->first();

            $controlName = "<a href='".route('compliance-project-control-show', [$record->project->id, $record->id])."'>".$record->name.'</a>';

            $data[] = [
                $record->project->standard,
                $record->project->name,
                $record->controlId,
                $controlName,
                $record->description,
                date('Y-m-d, g:i a', strtotime($latestEvidence->created_at)), // last uploaded
                $record->responsibleUser->full_name,
                $actions,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }

    public function downloadEvidences(Request $request, $controlID)
    {
        $projectControl = ProjectControl::where(function($query){
            if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor'])) {
                $query->where('responsible', $this->loggedUser->id);
            }
        })->findOrFail($controlID);

        
        $evidences = $projectControl->evidences(function ($query) use ($projectControl) {
            $query->where('deadline', $projectControl->deadline);

            if (!is_null($projectControl->rejected_at)) {
                $query->where('created_at', '>', $projectControl->rejected_at);
            }
        })->get();

        if ($evidences->count() == 1) {
            $evidence = $evidences->first();
            // decrypting file
            $encryptedContents = Storage::get($evidence->path);
            $baseName = basename($evidence->path);
            $decryptedContents = decrypt($encryptedContents);

            return response()->streamDownload(function () use ($decryptedContents) {
                echo $decryptedContents;
            }, $baseName);
        }

        // for multiple evidences making zip

        $zipFileName = 'evidences'.time().'.zip';
        $zipper = Zip::create($zipFileName);

        foreach ($evidences as $key => $evidence) {
            if ($evidence->type == 'document') {
                // decrypting file
                $encryptedContents = Storage::get($evidence->path);

                $baseName = basename($evidence->path);
                $decryptedContents = decrypt($encryptedContents);

                $zipper->addRaw($decryptedContents, $baseName);
            }
        }

        return $zipper;
    }
}
