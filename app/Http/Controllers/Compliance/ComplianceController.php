<?php

namespace App\Http\Controllers\Compliance;

use Illuminate\Http\Request;
use App\Models\DataScope\DataScope;
use App\Http\Controllers\Controller;
use App\Models\Compliance\ProjectControl;

class ComplianceController extends Controller
{
    public function getAllComplianceControls(Request $request, $projectControlId)
    {

        $projectControl = ProjectControl::withoutGlobalScope(DataScope::class)->findOrFail($projectControlId);

        $controlsToExclude = $projectControl->evidences()->where('type', 'control')->pluck('path')->toArray();
        $controlsToExclude[] = $projectControlId;

        $projectControls = ProjectControl::where(function ($q) use ($request, $controlsToExclude) {
            $q->whereNotIn('id', $controlsToExclude);

            $q->where('applicable', 1);

            $q->where('status', 'Implemented');

            if ($request->project_filter) {
                $q->where('project_id', $request->project_filter);
            }
        })->whereHas('project', function ($q) use ($request) {
            if ($request->standard_filter) {
                $q->where('standard_id', $request->standard_filter);
            }
        })->with('project')->paginate(10);

        $projectControls->getCollection()->transform(function ($item) {
            $standard = $item->project ? ($item->project->standard ?: '') : '';
            $projectName = $item->project ? $item->project->name : '';
            $controlName = $item->name ?: '';

            return [
                'project_name' => $projectName,
                'standard' => $standard,
                'control_id' => $item->controlId,
                'control_name' => $controlName,
                'desc' => $item->description ?: '',
                'frequency' => $item->frequency,
                'select' => '',

                'project_control_id' => $item->id
            ];
        });

        return response()->json(['data' => $projectControls]);
    }


}
