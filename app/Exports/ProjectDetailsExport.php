<?php

namespace App\Exports;

use App\Models\ThirdPartyRisk\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ProjectDetailsExport implements FromCollection, WithStrictNullComparison
{
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $this->project->load('questionnaire', 'vendor');
        $myCollection = new Collection([
            [
                'Project Name',
                'Questionnaire Name',
                'Vendor Name',
                'Launch Date',
                'Due Date',
                'Timezone',
                'Frequency',
                'Score',
                'Status'
            ]
        ]);
        $myCollection->add([
            $this->project->name,
            $this->project->questionnaire->name,
            $this->project->vendor->name,
            $this->project->launch_date,
            $this->project->due_date,
            $this->project->timezone,
            $this->project->frequency,
            $this->project->score,
            $this->project->project_status['status'],
        ]);
        $myCollection->add([
            ['Question', 'Answer']
        ]);
        $this->project->questionnaire->questions()
            ->with(['single_answer' => function ($q) {
                $q->where('project_id', $this->project->id)->latest();
            }])->each(function ($question) use ($myCollection) {
                $answer = $question->single_answer ? $question->single_answer->answer : 'Not answered yet.';
                    $myCollection->add([
                        $question->text,
                        $answer
                    ]);
            });
        return $myCollection;
    }
}
