<?php

namespace App\ScheduledTasks\Compliance;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Compliance\ProjectControl;
use App\ScheduledTasks\TenantScheduleTrait;
use App\Mail\Compliance\NotifyUnapprovedTaskStatusChanged;
use App\Models\TaskScheduleRecord\ComplianceProjectTaskScheduleRecord;

/**
 * PassDueTasksResetStatus.
 *
 * Next day of deadline, changes the tasks status due to lack of approved evidences
 */
class PassDueTasksResetStatus
{
    use TenantScheduleTrait;

    public function __invoke()
    {
        if(tenant('id')){
            $this->SetUpTenantMailContent(tenant('id'));
        }
        //Getting today save task schedule from compliance project task schedule record database
        $taskScheduleRecord = ComplianceProjectTaskScheduleRecord::whereDate('created_at', Carbon::today()->toDateString())->where('name', 'passDueTaskReset')->pluck('compliance_project_control_id')->toArray();

        $nextDayOfDeadline = date('Y-m-d', strtotime('-1 days'));

        $notImplimentedProjectControls = ProjectControl::where('applicable', 1)
                                        ->whereNotNull('responsible')
                                            ->whereNotNull('approver')
                                                ->whereNotNull('frequency')
                                                    ->where('frequency', '!=', 'One-Time')
                                                        ->where('status', '!=', 'Implemented')
                                                            ->where('deadline', $nextDayOfDeadline)
                                                            ->whereNotIn('id', $taskScheduleRecord)->get();

        //Exit function when count zero
        if (count($notImplimentedProjectControls) < 0) {
            exit;
        }

        /* Filtering out controls to be un-assigned */

        $targetUsers = collect();

        $groupByResponsibleUsersTasks = $notImplimentedProjectControls->unique('responsible');

        foreach ($groupByResponsibleUsersTasks as $key => $groupByResponsibleUsersTask) {
            $responsibleUser = collect($groupByResponsibleUsersTask->responsibleUser);
            $responsibleUser->put('user_type', 'responsible');

            $targetUsers->push($responsibleUser);
        }

        $groupByApproverUsersTasks = $notImplimentedProjectControls->unique('approver');

        foreach ($groupByApproverUsersTasks as $key => $groupByApproverUsersTask) {
            $approverUser = collect($groupByApproverUsersTask->approverUser);
            $approverUser->put('user_type', 'approver');

            $targetUsers->push($approverUser);
        }

        $this->sendUnapprovedTaskStatusChangedMail($targetUsers, $notImplimentedProjectControls);

        // updating status
        foreach ($notImplimentedProjectControls as $key => $projectControl) {
            $projectControl->status = 'Not Implemented';
            $projectControl->update();

            //store in compliance project task schedule record for single execution
            $todayComplianceProjectTaskSchedulePastDue = new ComplianceProjectTaskScheduleRecord();
            $todayComplianceProjectTaskSchedulePastDue->compliance_project_control_id = $projectControl->id;
            $todayComplianceProjectTaskSchedulePastDue->name = 'passDueTaskReset';
            $todayComplianceProjectTaskSchedulePastDue->save();
        }
    }

    protected function sendUnapprovedTaskStatusChangedMail($targetUsers, $tasks)
    {
        foreach ($targetUsers as $key => $targetUser) {
            if ($targetUser['user_type'] == 'responsible') {
                $taskLists = $tasks->where('responsible', $targetUser['id']);
            } elseif ($targetUser['user_type'] == 'approver') {
                $taskLists = $tasks->where('approver', $targetUser['id']);
            }
            $data = [
                    'greeting' => 'Hello '. decodeHTMLEntity($targetUser['first_name']).' '.decodeHTMLEntity($targetUser['last_name']),
                    'body' => 'The status for the following task(s) has been changed to<strong> Not Implemented </strong>due to lack of approved evidence.',
                    'task_lists' => $taskLists,
                    'action' => [
                        'action_title' => '',
                        'action_url' => route('compliance-dashboard'),
                        'action_button_text' => 'Go to my dashboard',
                    ],
                ];

            Mail::to($targetUser['email'])->send(new NotifyUnapprovedTaskStatusChanged($data));
        }
    }
}
