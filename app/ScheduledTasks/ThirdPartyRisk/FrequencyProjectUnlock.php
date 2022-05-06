<?php

namespace App\ScheduledTasks\ThirdPartyRisk;

use App\Models\ThirdPartyRisk\Project;
use App\Models\ThirdPartyRisk\ProjectActivity;
use App\Models\ThirdPartyRisk\ProjectEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\ScheduledTasks\TenantScheduleTrait;


class FrequencyProjectUnlock
{
    use TenantScheduleTrait;

    public function __invoke()
    {
        if(tenant('id')){
            $this->SetUpTenantMailContent(tenant('id'));
        }

        $projects = Project::with('email')->where("status", "archived")->get();

        foreach ($projects as $project) {
            $initial_due_date = Carbon::createFromFormat('Y-m-d H:i:s', $project->due_date,'UTC')->setTimezone($project->timezone);
            $initial_due_date->toDateTimeString();

            $frequency = $project->frequency;

            switch ($frequency) {
                case "Weekly":
                    $next_activation_date = $initial_due_date->copy()->addWeek();
                    break;
                case "Biweekly":
                    $next_activation_date = $initial_due_date->copy()->addWeeks(2);
                    break;
                case "Monthly":
                    $next_activation_date = $initial_due_date->addMonth();
                    break;
                case "Bi-anually":
                    $next_activation_date = $initial_due_date->addMonths(6);
                    break;
                case "Annually":
                    $next_activation_date = $initial_due_date->addYear();
                    break;
                default:
                    $next_activation_date = Carbon::now($project->timezone);
            }

            $now = Carbon::now($project->timezone);

            if ($now->greaterThanOrEqualTo($next_activation_date)) {
                $initial_launch_date = Carbon::createFromFormat('Y-m-d H:i:s', $project->launch_date,'UTC')->setTimezone($project->timezone);
                //time vendors had to answer the questionnaire for the project;
                $initial_interval = $initial_due_date->diffInDays($initial_launch_date);
                $new_due_date = $now->copy()->addDays($initial_interval);

                $project->update([
                    'launch_date' => $now->setTimezone('UTC')->toDateTimeString(),
                    'due_date' => $new_due_date->setTimezone('UTC')->toDateTimeString(),
                    'status' => 'active'
                ]);

                if ($project->email) {
                    $email = $project->email;
                    $email->status = "pending";
                    $email->save();
                } else {
                    ProjectEmail::create([
                        'project_id' => $project->id,
                        'token' => encrypt($project->id . '-' . $project->vendor_id . date('r', time())),
                    ]);
                }

                //delete old activities, as new cycle will begin and that one will be followed.
                ProjectActivity::where('project_id', $project->id)->delete();

                ProjectActivity::create([
                    'project_id' => $project->id,
                    'activity' => 'New cycle started',
                    'type' => 'new-cycle',
                ]);
                
                Log::info("Project was unlocked as set by frequency", [
                    'project_id' => $project->id,
                    'frequency' => $frequency
                ]);
            }

           
        }
    }

}
