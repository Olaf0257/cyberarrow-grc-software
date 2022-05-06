<?php

namespace App\Exports\Project;

use App\Models\Compliance\Project;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProjectExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $project = Project::where('id', $this->id)->with('controls')->first();

        $total = $project->controls()->count();
        $notApplicable = $project->controls()->where('applicable', 0)->count();
        $implemented = $project->controls()->where('applicable', 1)->where('status', 'Implemented')->count();
        $notImplementedControl = $project->controls()->where('applicable', 1)->where('status', 'Not Implemented')->count();
        $rejectedControls = $project->controls()->Where('status', 'Rejected')->count();
        $notImplemented = $notImplementedControl + $rejectedControls;
        $underReview = $project->controls()->where('applicable', 1)->where('status', 'Under Review')->count();

        //getting only applicable count and convert into percentage

        $totalPercentage = $implemented + $notImplemented + $underReview;
        $perImplemented = ($totalPercentage > 0) ? ($implemented / $totalPercentage) * 100 : 0;
        $perUnderReview = ($totalPercentage > 0) ? ($underReview / $totalPercentage) * 100 : 0;
        $perNotImplemented = ($totalPercentage > 0) ? ($notImplemented / $totalPercentage) * 100 : 0;

        $excelCollection = [
            [
                'Project Name',
                $project->name,
            ],
            [
                'Project Description',
                $project->description,
            ],
            [
                'Standard',
                $project->standard,
            ],
            [
                'Date',
                $project->created_at->format('d/m/Y'),
            ],
            [
                'Total Controls:',
                $total,
            ],
            [
                'Not Applicable:',
                ($notApplicable > 0 ? $notApplicable : '0'),
            ],
            [
                'Implemented Controls:',
                ($implemented > 0 ? $implemented : '0'),
            ],
            [
                'Under Review:',
                ($underReview > 0 ? $underReview : '0'),
            ],
            [
                'Not Implemented Controls:',
                ($notImplemented > 0 ? $notImplemented : '0'),
            ],
            [
                'Implemented %',
                round($perImplemented, 2).'%',
            ],
            [
                'Not Implemented %',
                round($perNotImplemented, 2).'%',
            ],
            [
                'Under Review %',
                ($perUnderReview > 0 ? round($perUnderReview, 2) : 0).'%',
            ],
            [
                '',
                '',
            ],
            [
                '',
                '',
            ],

            [
                '',
                '',
                '',
                'Control ID',
                'Control Name',
                'Control Description',
                'Status',
                'Responsible',
                'Approver',
                'Deadline',
                'Frequency',
                'Applicable',
            ],
        ];

        foreach ($project['controls'] as $p) {
            $excelCollection[] = [
                '',
                '',
                '',
                $p->controlID,
                $p->name,
                $p->description,
                $p->status,
                $p->responsible ? $p->responsibleUser->full_name : '',
                $p->approver ? $p->approverUser->full_name : '',
                $p->deadline ? $p->deadline : '',
                $p->frequency ? $p->frequency : '',
                $p->applicable == 1 ? 'Yes' : 'No',
             ];
        }

        return new Collection(
            $excelCollection
        );
    }
}
