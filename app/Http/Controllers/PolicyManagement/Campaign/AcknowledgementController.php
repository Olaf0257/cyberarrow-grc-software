<?php

namespace App\Http\Controllers\PolicyManagement\Campaign;

use App\Http\Controllers\Controller;
use App\Mail\PolicyManagement\CampaignCompleted;
use App\Models\PolicyManagement\Campaign\Campaign;
use App\Models\PolicyManagement\Campaign\CampaignAcknowledgment;
use App\Models\PolicyManagement\Campaign\CampaignAcknowledgmentUserToken;
use App\Models\PolicyManagement\Campaign\CampaignPolicy;
use App\Models\PolicyManagement\Campaign\CampaignActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AcknowledgementController extends Controller
{
    protected $viewBasePath = 'policy-management.campaign.acknowledgement';

    public function show(Request $request, $token)
    {
        $campaignAcknowledgmentUserToken = CampaignAcknowledgmentUserToken::where('token', $token)->with(['user', 'campaign'])->first();

        if (!$campaignAcknowledgmentUserToken) {
            abort(404);
        }

        $userId = $campaignAcknowledgmentUserToken->user_id;
        $campaignId = $campaignAcknowledgmentUserToken->campaign_id;
        $campaignAcknowledgments = CampaignAcknowledgment::where('campaign_id', $campaignAcknowledgmentUserToken->campaign_id)
            ->where('user_id', $userId)
                ->where('status', 'pending')
                    ->with(['user', 'policy'])
                        ->get();
        if ($campaignAcknowledgments->count() == 0) {
            $user = $campaignAcknowledgmentUserToken->user;

            return Inertia::render('policy-management/campaign-policy-acknowledgement/acknowledged/AcknowledgedPage', [
                'user' => $user
            ]);
        }

        CampaignActivity::create([
            'campaign_id' => $campaignId,
            'activity' => 'Acknowledgment link clicked',
            'type' => 'clicked-link',
            'user_id' => $userId,
        ]);


        return Inertia::render('policy-management/campaign-policy-acknowledgement/show/ShowPage', [
            'campaignAcknowledgments' => $campaignAcknowledgments,
            'campaignAcknowledgmentUserToken' => $campaignAcknowledgmentUserToken
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'campaign_acknowledgment_user_token' => 'required',
            'agreed_policy' => 'required|array|min:1',
        ], [
            'agreed_policy.required' => 'Please click the checkbox to agree to the above policy.',
        ]);

        $campaignAcknowledgmentUserToken = CampaignAcknowledgmentUserToken::where('token', $request->campaign_acknowledgment_user_token)->first();

        if (!$campaignAcknowledgmentUserToken) {
            abort(404);
        }

        $campaignAcknowledgments = CampaignAcknowledgment::where('campaign_id', $campaignAcknowledgmentUserToken->campaign_id)
            ->where('user_id', $campaignAcknowledgmentUserToken->user_id)
                ->whereIn('token', $request->agreed_policy)
                ->with('policy')
                    ->get();

        if ($campaignAcknowledgments->count() == 0) {
            abort(404);
        }

        $result = \DB::transaction(function () use ($campaignAcknowledgmentUserToken, $campaignAcknowledgments, $request) {
            $acknowledgedPolicies = [];

            foreach ($campaignAcknowledgments as $key => $campaignAcknowledgment) {
                $campaignAcknowledgment->status = 'completed';
                $campaignAcknowledgment->token = null;
                $campaignAcknowledgment->update();

                $acknowledgedPolicies[] = $campaignAcknowledgment->policy->display_name;
            }

            // Creating campaign activity
            CampaignActivity::create([
                'campaign_id' => $campaignAcknowledgmentUserToken->campaign_id,
                'activity' => implode(', ', $acknowledgedPolicies).' policy(ies) are acknowledged',
                'type' => 'policy-acknowledged',
                'user_id' => $campaignAcknowledgmentUserToken->user_id,
            ]);

            // Checking campaing is completed
            $pendingAcknowledgments = CampaignAcknowledgment::where('campaign_id', $campaignAcknowledgmentUserToken->campaign_id)
                ->where('status', 'pending')->count();

            if ($pendingAcknowledgments == 0) {
                // archiving the campaign

                $campaign = Campaign::findOrFail($campaignAcknowledgmentUserToken->campaign_id);

                $campaign->status = 'archived';
                $campaign->update();

                Mail::to($campaign->owner->email)->send(new CampaignCompleted($campaign));
            }
        });

        $user = $campaignAcknowledgments->first()->user;

        return redirect()->route('policy-management.campaigns.acknowledgement.completed')->with([
            'data' => [
                'campaignAcknowledgments' => $campaignAcknowledgments,
            'user' => $user
            ]
        ]);
    }

    public function showCompletedPage(Request $request)
    {
        $data = $request->session()->has('data') ? $request->session()->get('data'): [];
        return Inertia::render('policy-management/campaign-policy-acknowledgement/completed/CompletedPage', $data);
    }

    public function getNewUrlForS3Policy(Request $request){
        $campaignAcknowledgmentUserToken = CampaignAcknowledgmentUserToken::where('token', $request->token)->with(['user', 'campaign'])->first();

        if (!$campaignAcknowledgmentUserToken) {
            abort(404);
        }

        $userId = $campaignAcknowledgmentUserToken->user_id;
        $campaignId = $campaignAcknowledgmentUserToken->campaign_id;
        $campaignAcknowledgments = CampaignAcknowledgment::where('campaign_id', $campaignAcknowledgmentUserToken->campaign_id)
            ->where('user_id', $userId)
                ->where('status', 'pending')
                    ->with(['user', 'policy'])
                        ->get();
        return response()->json($campaignAcknowledgments);
    }
}
