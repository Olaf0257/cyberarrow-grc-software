<?php

namespace App\ScheduledTasks\PolicyManagement;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\ScheduledTasks\TenantScheduleTrait;
use App\Mail\PolicyManagement\Acknowledgement;
use App\Models\PolicyManagement\Campaign\Campaign;
use App\Models\PolicyManagement\Campaign\CampaignActivity;
use App\Models\PolicyManagement\Campaign\CampaignAcknowledgmentUserToken;

class SendAcknowledgementEmail
{
    use TenantScheduleTrait;
    
    public function __invoke()
    {
        if(tenant('id')){
            $this->SetUpTenantMailContent(tenant('id'));
        }
        $campaigns = Campaign::where('acknowledgement_email_sent', 0)->with('groups', 'groups.users', 'policies')->get();

        foreach ($campaigns as $campaign) {
            $nowDateTime = Carbon::now($campaign->timezone);
            $launchDate = Carbon::createFromFormat('Y-m-d H:i:s', $campaign->launch_date,'UTC')->setTimezone($campaign->timezone);


            if ($launchDate->lessThanOrEqualTo($nowDateTime)) {
                $acknowledgements = $campaign->acknowledgements->where('status', 'pending');
                $policies = $campaign->policies;

                $totalEmailSentCount = 0;

                foreach ($acknowledgements->groupBy('user_id') as $index => $acknowledgementGroup) {
                    $user = $acknowledgementGroup->first()->user;

                    $acknowledgmentUserToken = CampaignAcknowledgmentUserToken::where('campaign_id', $campaign->id)->where('user_id', $user->id)->first();

                    if ($acknowledgmentUserToken) {
                        try {
                            Mail::to($user->email)->send(new Acknowledgement($acknowledgmentUserToken, $campaign, $acknowledgementGroup, $user));

                            // When email sent successfully
                            CampaignActivity::create([
                                'campaign_id' => $campaign->id,
                                'activity' => 'Email Sent on Campaign start',
                                'type' => 'email-sent',
                                'user_id' => $user->id,
                            ]);

                            ++$totalEmailSentCount;
                        } catch (\Exception $ex) {
                            echo $ex;
                            CampaignActivity::create([
                                'campaign_id' => $campaign->id,
                                'activity' => 'Error Sending Email',
                                'type' => 'email-sent-error',
                                'user_id' => $user->id,
                            ]);
                        }
                    }
                }

                // acknowledgement_email_sent column updating
                if ($totalEmailSentCount > 0) {
                    $campaign->acknowledgement_email_sent = 1;
                    $campaign->update();
                }
            }
        }
    }
}
