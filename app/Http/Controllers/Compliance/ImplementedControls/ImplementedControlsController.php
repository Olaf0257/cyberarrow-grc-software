<?php

namespace App\Http\Controllers\Compliance\ImplementedControls;

use Zip;
use Auth;
use Illuminate\Http\Request;
use App\Utils\RegularFunctions;
use App\Models\Compliance\Project;
use App\Models\Compliance\Standard;
use App\Http\Controllers\Controller;
use App\Models\Compliance\Evidence;
use Illuminate\Support\Facades\Storage;
use App\Models\Compliance\ProjectControl;
use Inertia\Inertia;

class ImplementedControlsController extends Controller
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
        $managedContributors[] = [
            'value' => 0,
            'label' => "All Users"
        ];
        foreach ($taskContributors as $key => $eachContributor) {
            $managedContributors[] = ['value'=>$eachContributor,'label'=>$key];
        }

        $data = [
            'taskContributors' => $managedContributors,
        ];
        return Inertia::render('controls/controls',$data);
    }

    public function getControlsData(){
        $taskContributors = RegularFunctions::getControlContributorListArray();
        $allStandards = Standard::whereHas('projects', function($query){
            $query->whereHas('controls', function($query){
                if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor'])) {
                    $query->where('responsible', $this->loggedUser->id)->orWhere('approver', $this->loggedUser->id);
                }
            });
        })->get();

        $managedStandards[] = [
            'value' => 0,
            'label' => "Select Standards"
        ];

        foreach ($allStandards as $key => $eachStandard) {
            $managedStandards[] = ['value'=>$eachStandard['id'],'label'=>$eachStandard['name']];
        }
        return response()->json(compact('taskContributors', 'managedStandards', 'allStandards'));
    }

    public function getImplementedControlsData(Request $request)
    {
        $baseQuery = ProjectControl::where('status', 'Implemented')
            ->where(function ($query) use ($request) {
                if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor'])) {
                    $query->where('responsible', $this->loggedUser->id)->orWhere('approver', $this->loggedUser->id);
                }

                $controlName = $request->control_name;
                $controlID = $request->controlID;

                if ($controlName) {
                    $query->where('name', 'LIKE', '%' . $controlName . '%');
                }

                if ($controlID) {
                    $query->whereRaw("CONCAT(`primary_id`, `id_separator`, `sub_id`) LIKE ?", ['%' . $controlID . '%']);
                }
            })
            ->whereHas('responsibleUser', function ($query) use ($request) {
                $responsibleUser = $request->responsible_user;

                if ($this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor','Contributor'])) {
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

        // $records = $baseQuery->get();
        $records = $baseQuery->paginate($request->per_page ?? 10);

        $data = [];

        foreach ($records as $key => $record) {

            $linkEvidences = $record->evidences->where('type', 'link');
            $controlEvidences = $record->evidences->where('type', 'control');
            $textEvidences = $record->evidences->where("type", "text");
                    //checking if the evidences has link or control as a type: if so store each one's in array
                    $controlEvidences_urls = [];
                    if ($linkEvidences->count() || $controlEvidences->count()) {
                        if ($controlEvidences->count()) {
                            foreach ($controlEvidences as $controlEvidence) {
                                $controlEvidences_urls[] = route('project-control-linked-controls-evidences-view', [$controlEvidence->projectControl->project_id, $controlEvidence->path, $controlEvidence->project_control_id]);
                            }
                        }
                    }

                    $documentEvidences=[];
                    if ($record->evidences->where('type', 'document')->count()) {
                        array_push($documentEvidences,route('compliance.implemented-controls.download-evidences', $record->id));
                    }
                    $txtEvidenceActions=[];
                    foreach($textEvidences as $te){
                        array_push($txtEvidenceActions,$te);
                    }
                    $linkEvidencesActions=[];
                    foreach($linkEvidences as $le){
                        array_push($linkEvidencesActions,$le);
                    }
                    $record->actions_list=[$txtEvidenceActions,$linkEvidencesActions,$controlEvidences_urls,$documentEvidences];
                    $documentEvidences=[];$txtEvidenceActions=[];$linkEvidencesActions=[];$controlEvidences_urls=[];

            $latestEvidence = $record->evidences()->latest('id')->first();

            // $controlName = "<a href='" . route('compliance-project-control-show', [$record->project->id, $record->id]) . "'>" . $record->name . '</a>';
            $controlNameObject = [
                'name' => $record->name,
                'url' => route('compliance-project-control-show', [$record->project->id, $record->id])
            ];

            $data[] = [
                $record->project->standard,
                $record->project->name,
                $record->controlId,
                $controlNameObject,
                $record->description,
                date('d-m-Y, g:i a', strtotime($latestEvidence->created_at)), // last uploaded
                $record->responsibleUser->full_name,
                $record->actions_list
            ];
        }

        $records->setCollection(collect($data));
        return response()->json([
            'data' => $records
        ]);
    }

    public function downloadEvidences(Request $request, $controlID)
    {
        $projectControl = ProjectControl::where(function ($query) {
            if (!$this->loggedUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor'])) {
                $query->where('responsible', $this->loggedUser->id);
            }
        })->findOrFail($controlID);

        $documentEvidences = Evidence::where('project_control_id',$projectControl->id)
            ->where('type', 'document')
            ->where('deadline', $projectControl->deadline)
            ->when($projectControl->rejected_at, function ($query) use ($projectControl){
                $query->where('created_at', '>', $projectControl->rejected_at);
            })
            ->get();

        // $documentEvidences = $projectControl->evidences(function ($query) use ($projectControl) {
        //     $query->where('type', 'document')->where('deadline', $projectControl->deadline);

        //     if (!is_null($projectControl->rejected_at)) {
        //         $query->where('created_at', '>', $projectControl->rejected_at);
        //     }
        // })->get();

        if ($documentEvidences->count() == 1) {
            $evidence = $documentEvidences->first();
            // decrypting file
            $encryptedContents = Storage::get($evidence->path);
            $baseName = basename($evidence->path);
            $decryptedContents = decrypt($encryptedContents);

            return response()->streamDownload(function () use ($decryptedContents) {
                echo $decryptedContents;
            }, $baseName);
        }

        // for multiple evidences making zip

        $zipFileName = 'evidences' . time() . '.zip';
        $zipper = Zip::create($zipFileName);


        foreach ($documentEvidences as $evidence) {
            // decrypting file
            $encryptedContents = Storage::get($evidence->path);

            $baseName = basename($evidence->path);
            $decryptedContents = decrypt($encryptedContents);

            $zipper->addRaw($decryptedContents, $baseName);
        }

        return $zipper;
    }
}
