<?php

namespace App\ScheduledTasks\ThirdPartyRisk;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\ThirdPartyRisk\Project;
use App\ScheduledTasks\TenantScheduleTrait;
use App\Models\ThirdPartyRisk\ProjectActivity;
use App\Mail\ThirdPartyRisk\Questionnaire as QuestionnaireMail;

class SendVendorProjectEmail
{
    use TenantScheduleTrait;

    public function __invoke()
    {

        Log::info('Accessed SendVendorProjectEmail cron.');
        if(tenant('id')){
            $this->SetUpTenantMailContent(tenant('id'));
        }

        $projects = Project::whereDoesntHave('activities', function($query) {
            $query->where('type', 'email-sent');
        })->with('vendor', 'questionnaire', 'email')->get();

        Log::info('Accessed SendVendorProjectEmail cron.', ['project_found_count' => $projects->count()]);

        foreach ($projects as $project) {
            Log::info('Project', ['project name' => $project->name]);
            $now_date_time = Carbon::now($project->timezone);
            $launch_date = Carbon::createFromFormat('Y-m-d H:i:s', $project->launch_date,'UTC')->setTimezone($project->timezone);
            Log::info('Timezones used.', ['now' => $now_date_time, 'launch_date' => $launch_date]);

            if ($launch_date->lessThanOrEqualTo($now_date_time)) {
                Log::info('launch date is less than or equal to now', ['now' => $now_date_time, 'launch_date' => $launch_date]);
                $vendor = $project->vendor;
                $vendor_token = $project->email;
                Log::info('Vendor data', ['vendor' => $vendor, 'vendor token' => $vendor_token]);

                if ($vendor_token) {
                    try {
                        Mail::to($vendor->email)->send(new QuestionnaireMail($vendor_token, $project, $vendor));
                        Log::info('Mail should be sent at this point');

                        ProjectActivity::create([
                            'project_id' => $project->id,
                            'activity' => 'Email Sent on project start',
                            'type' => 'email-sent',
                        ]);
                    } catch (\Exception $e) {
                        Log::info('there was an error. On catch', ['e' => $e]);

                        ProjectActivity::create([
                            'project_id' => $project->id,
                            'activity' => 'Error Sending Email',
                            'type' => 'email-sent-error',
                        ]);
                    }
                }
            }
        }
    }
}
