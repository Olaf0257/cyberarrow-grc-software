<?php

namespace App\ScheduledTasks\Compliance;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Compliance\ProjectControl;
use App\ScheduledTasks\TenantScheduleTrait;
use App\Models\TaskScheduleRecord\ComplianceProjectTaskScheduleRecord;
use App\Mail\Compliance\TaskDeadlineReminder as TaskDeadlineReminderMail;

class TaskDeadlineReminder
{
    use TenantScheduleTrait;

    public function __invoke()
    {
        if(tenant('id')){
            $this->SetUpTenantMailContent(tenant('id'));
        }
        // Two week before deadline tasks

        $twoWeekBeforeDeadlineTasks = $this->getTaskListsByDeadline(strtotime('+2 week'));

        //checking if count greater than zero
        if (count($twoWeekBeforeDeadlineTasks) > 0) {
            $twoWeekBeforeDeadlineTasksResponsibleUsers = $twoWeekBeforeDeadlineTasks->unique('responsible');

            $this->sendTaskDeadlineReminderMail($twoWeekBeforeDeadlineTasksResponsibleUsers, $twoWeekBeforeDeadlineTasks);
        }

        // One week before deadline tasks

        $oneWeekBeforeDeadlineTasks = $this->getTaskListsByDeadline(strtotime('+1 week'));

        //checking if count greater than zero
        if (count($oneWeekBeforeDeadlineTasks) > 0) {
            $oneWeekBeforeDeadlineTasksResponsibleUsers = $oneWeekBeforeDeadlineTasks->unique('responsible');

            $this->sendTaskDeadlineReminderMail($oneWeekBeforeDeadlineTasksResponsibleUsers, $oneWeekBeforeDeadlineTasks);
        }

        // Two days before the deadline tasks

        $twoDaysBeforeDeadlineTasks = $this->getTaskListsByDeadline(strtotime('+2 days'));

        //checking if count greater than zero
        if (count($twoDaysBeforeDeadlineTasks) > 0) {
            $twoDaysBeforeDeadlineTasksResponsibleUsers = $twoDaysBeforeDeadlineTasks->unique('responsible');

            $this->sendTaskDeadlineReminderMail($twoDaysBeforeDeadlineTasksResponsibleUsers, $twoDaysBeforeDeadlineTasks);
        }

        // One days before the deadline tasks

        $oneDaysBeforeDeadlineTasks = $this->getTaskListsByDeadline(strtotime('+1 days'));

        //checking if count greater than zero
        if (count($oneDaysBeforeDeadlineTasks) > 0) {
            $oneDaysBeforeDeadlineTasksResponsibleUsers = $oneDaysBeforeDeadlineTasks->unique('responsible');

            $this->sendTaskDeadlineReminderMail($oneDaysBeforeDeadlineTasksResponsibleUsers, $oneDaysBeforeDeadlineTasks);
        }
    }

    /**
     * Sends the emails to responbile user to remind task deadline.
     */
    protected function sendTaskDeadlineReminderMail($responsibleUsers, $tasks)
    {
        foreach ($responsibleUsers as $key => $uniqueResponsible) {
            $taskLists = $tasks->where('responsible', $uniqueResponsible->responsible);

            $data = [
                        'greeting' => 'Hello '.$uniqueResponsible->responsible_firstname.' '.$uniqueResponsible->responsible_lastname,
                        'title' => 'You have '.$taskLists->count().' tasks due on '.$uniqueResponsible->deadline,
                        'body' => "Please find below a friendly reminder of tasks assigned to you that are due to be completed on {$uniqueResponsible->deadline}",
                        'task_lists' => $taskLists,
                        'action' => [
                            'action_title' => '',
                            'action_url' => route('compliance-dashboard'),
                            'action_button_text' => 'Go to my dashboard',
                        ],
                    ];

            Mail::to($uniqueResponsible->responsible_user_email)->send(new TaskDeadlineReminderMail($data));

            //store in compliance project task schedule record for single execution
            $todayComplianceProjectTaskSchedule = new ComplianceProjectTaskScheduleRecord();
            $todayComplianceProjectTaskSchedule->compliance_project_control_id = $uniqueResponsible->id;
            $todayComplianceProjectTaskSchedule->name = 'taskDeadlineReminder';
            $todayComplianceProjectTaskSchedule->save();
        }
    }

    /**
     * Gets the list of task matchine the provided deadline.
     */
    protected function getTaskListsByDeadline($deadline)
    {
        //Getting today save task schedule name from compliance project task schedule record database
        $taskScheduleRecord = ComplianceProjectTaskScheduleRecord::whereDate('created_at', Carbon::today()->toDateString())->where('name', 'taskDeadlineReminder')->pluck('compliance_project_control_id')->toArray();

        $taskLists = ProjectControl::whereNotNull('responsible')->whereNotNull('approver')
                                                                ->whereNotNull('deadline')
                                                                    ->whereNull('approved_at')
                                                                        ->where('deadline', date('Y-m-d', $deadline))
                                                                                    ->join('admins as responsible_user', 'compliance_project_controls.responsible', '=', 'responsible_user.id')
                                                                                    ->whereNotIn('compliance_project_controls.id', $taskScheduleRecord)
                                                                                        ->get([
                                                                                            'compliance_project_controls.id',
                                                                                            'responsible',
                                                                                            'deadline',
                                                                                            'responsible_user.first_name as responsible_firstname',
                                                                                            'responsible_user.last_name as responsible_lastname',
                                                                                            'responsible_user.email as responsible_user_email',
                                                                                            'name',
                                                                                            'project_id',
                                                                                        ]);

        return $taskLists;
    }
}
