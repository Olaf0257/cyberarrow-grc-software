<?php

namespace App\ScheduledTasks\PolicyManagement;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PolicyManagement\AutoReminder;
use App\ScheduledTasks\TenantScheduleTrait;
use App\Models\PolicyManagement\Campaign\Campaign;
use App\Models\PolicyManagement\Campaign\CampaignActivity;
use App\Models\TaskScheduleRecord\PolicyManagementTaskScheduleRecord;
use App\Models\PolicyManagement\Campaign\CampaignAcknowledgmentUserToken;

class SendAutoReminderEmail
{
    use TenantScheduleTrait;

    public function __invoke()
    {
        if(tenant('id')){
            $this->SetUpTenantMailContent(tenant('id'));
        }
        
        $campaigns = Campaign::where('status', 'active')->get();

        //Getting today save task schedule from policy management task schedule record database
        $taskScheduleRecord = PolicyManagementTaskScheduleRecord::whereDate('created_at', Carbon::today()->toDateString())->pluck('campaign_id')->toArray();

        foreach ($campaigns as $campaign) {
            $nowDateTime = Carbon::now($campaign->timezone);
            $campaignLaunchDate = Carbon::createFromFormat('Y-m-d H:i:s', $campaign->launch_date,'UTC')->setTimezone($campaign->timezone)->format('Y-m-d');
            $nowDate = $nowDateTime->format('Y-m-d');
            $dueDate = Carbon::createFromFormat('Y-m-d H:i:s', $campaign->due_date,'UTC')->setTimezone($campaign->timezone);

            //restrict reminder campaign mail if campaign is just started
            if ($campaignLaunchDate == $nowDate) {
                continue;
            }

            //restrict reminder for dublicate campaign
            if (!in_array($campaign->id, $taskScheduleRecord)) {
                // Sending reminder email one day before due date
                $oneDayBeforeDueDate = clone $nowDateTime;
                if ($oneDayBeforeDueDate->modify('+1 day')->format('Y-m-d') == $dueDate->format('Y-m-d')) {
                    $this->sendReminderEmail($campaign);
                }

                //Sending reminder email two day before due date
                $twoDayBeforeDueDate = clone $nowDateTime;
                if ($twoDayBeforeDueDate->modify('+2 day')->format('Y-m-d') == $dueDate->format('Y-m-d')) {
                    $this->sendReminderEmail($campaign);
                }

                // Sending reminder email 1 week before
                $oneWeekBeforeDueDate = clone $nowDateTime;
                if ($oneWeekBeforeDueDate->modify('+1 week')->format('Y-m-d') == $dueDate->format('Y-m-d')) {
                    $this->sendReminderEmail($campaign);
                }

                $todayPolicyManagementTaskScheduleAutoReminder = new PolicyManagementTaskScheduleRecord();
                $todayPolicyManagementTaskScheduleAutoReminder->campaign_id = $campaign->id;
                $todayPolicyManagementTaskScheduleAutoReminder->save();
            }
        }
    }

    private function sendReminderEmail($campaign)
    {
        $acknowledgements = $campaign->acknowledgements->where('status', 'pending');

        foreach ($acknowledgements->groupBy('user_id') as $index => $acknowledgementGroup) {
            $user = $acknowledgementGroup->first()->user;
            $acknowledgmentUserToken = CampaignAcknowledgmentUserToken::where('campaign_id', $campaign->id)->where('user_id', $user->id)->first();

            try {
                Mail::to($user->email)->send(new AutoReminder($acknowledgmentUserToken, $campaign, $acknowledgementGroup, $user));

                CampaignActivity::create([
                    'campaign_id' => $campaign->id,
                    'activity' => 'Reminder Email Sent',
                    'type' => 'email-sent',
                    'user_id' => $user->id,
                ]);
            } catch (\Exception $ex) {
                CampaignActivity::create([
                    'campaign_id' => $campaign->id,
                    'activity' => 'Error Sending Email',
                    'type' => 'email-sent-error',
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
