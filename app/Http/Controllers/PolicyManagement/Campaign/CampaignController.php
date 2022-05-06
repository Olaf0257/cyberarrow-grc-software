<?php

namespace App\Http\Controllers\PolicyManagement\Campaign;

use App\Exports\PolicyManagement\Campaigns\usersStatusExport;
use App\Http\Controllers\Controller;
use App\Models\GlobalSettings\GlobalSetting;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Mail\PolicyManagement\AutoReminder;
use App\Models\PolicyManagement\Campaign\Campaign;
use App\Models\PolicyManagement\Campaign\CampaignAcknowledgment;
use App\Models\PolicyManagement\Campaign\CampaignAcknowledgmentUserToken;
use App\Models\PolicyManagement\Campaign\CampaignActivity;
use App\Models\PolicyManagement\Group\Group;
use App\Models\PolicyManagement\Policy;
use App\Models\PolicyManagement\User;
use App\Traits\Timezone;
use Auth;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\PolicyManagement\CampaignResource;

class CampaignController extends Controller
{
    protected $viewBasePath = 'policy-management.campaign.';
    protected $authUser;
    use Timezone;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            $this->authUser = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    /**
     * Method index
     *
     * @param Request $request [explicite description]
     *
     * @return void
     */
    public function index(Request $request)
    {
        $timezones = $this->appTimezone();

        /* Sharing page title to view */
        view()->share('pageTitle', 'Campaigns - Policy Management');

        return Inertia::render('policy-management/campaign-page/CampaignPage', [
            'timezones' => $timezones
        ]);
    }

    public function campaignList(Request $request)
    {
        $campaignStatus = $request->campaign_status;
        $campaigns = Campaign::where(function ($query) use ($request, $campaignStatus) {
            if ($campaignStatus) {
                $query->where('status', $campaignStatus);
            }

            if ($request->campaign_name) {
                $query->where('name', 'like', '%'.$request->campaign_name.'%');
            }
        })->with('policies', 'groups')
            ->orderBy('id', 'desc')
                ->get();

        return CampaignResource::collection($campaigns)->additional(['success' => true]);
    }

    /**
     * Returns the campaign users.
     **/
    public function renderCampaignUsers(Request $request, $campaignId)
    {

        $campaign = Campaign::withoutGlobalScopes()->with('groups')->where('id', $campaignId)->first();


        if (!$campaign) {
            return response()->json([
                'success' => false,
            ]);
        }

        $campaignUsers = $campaign->users()->where(function ($query) {
            if (request('filter_by_user_name')) {
                $query->whereRaw("concat(first_name, ' ', last_name) like '%".request('filter_by_user_name')."%' ");
            }
        })->with('activities')->paginate($request->page_length);

        return response()->json([
            'success' => true,
            'data' => [
                'campaign' => $campaign,
                'campaignUsers' => $campaignUsers
            ]
        ]);
    }

    /***
     * Creating the create campaigns
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_scope' => 'required',
            'name' => 'required',
            'policies' => 'required|array|min:1',
            'launch_date' => 'required|date|after:'.date('Y/m/d'),
            'due_date' => 'nullable|date|after:launch_date',
            'timezone' => 'required',
            'groups' => 'required|array|min:1',
            'auto_enroll_users' => [
                'required',
                Rule::in(['yes', 'no']),
            ]
        ]);

        $input = $request->all();

        $newCampaign = \DB::transaction(function () use ($request, $input) {
            // formatting dates
            $launchDate = new \DateTime($input['launch_date']);
            $launchDate = $launchDate->format('Y-m-d H:i:s');

            $dueDate = new \DateTime($input['due_date']);
            $dueDate = $dueDate->format('Y-m-d H:i:s');

            $campaign = Campaign::create([
                'name' => $input['name'],
                'owner_id' => $this->authUser->id,
                'launch_date' => $launchDate,
                'due_date' => $dueDate,
                'timezone' => $input['timezone'],
                'auto_enroll_users' => $request['auto_enroll_users'],
            ]);

            /* saving campaign policies */

            /* Getting the policies */
            $policies = Policy::whereIn('id', $request->policies)->get();

            /* Storing the campaign policies*/
            foreach ($policies as $policy) {
                $campaignPolicy = $campaign->policies()->create([
                    'policy_id' => $policy->id,
                    'display_name' => $policy->display_name,
                    'type' => $policy->type,
                    'path' => $policy->path,
                    'version' => $policy->version,
                    'description' => $policy->description,
                ]);

                /* Storing the policy file */
                if ($policy->type == 'document') {
                    $policyFile = Storage::url('public/'.$policy->path);
                    $filePath = "policy-management/campaign-policies/{$campaignPolicy->id}";
                    $fileName = basename($policyFile);
                    // Store the Content
                    Storage::copy('public/'.$policy->path,'public/'.$filePath.'/'.$fileName);
                    $campaignPolicy->update([
                        'path' => $filePath.'/'.$fileName,
                    ]);
                }
            }

            /* GETTING THE GROUPS Within the data scopes */
            $groups = Group::whereIn('id', $request->groups)->get();

            /* Saving campaign group */
            foreach ($groups as $group) {
                /* Creating campaign groups*/
                $campaignGroup = $campaign->groups()->create([
                    'group_id' => $group->id,
                    'name' => $group->name,
                ]);

                /* creating campaign group users >> users with same email are not created*/
                foreach ($group->users as $key => $user) {
                    $alreadyAdded = $campaign->users()->where('email', $user->email)
                            ->first();
                    /*Going to next iteration when user already added*/
                    if ($alreadyAdded) {
                        break;
                    }

                    $campaignGroupUser = $campaignGroup->users()->create([
                                            'email' => $user->email,
                                            'first_name' => $user->first_name,
                                            'last_name' => $user->last_name,
                                        ]);

                    CampaignAcknowledgmentUserToken::create([
                                            'campaign_id' => $campaign->id,
                                            'user_id' => $campaignGroupUser->id,
                                            'token' => encrypt($campaign->id.'-'.$campaignGroupUser->id.date('r', time())),
                                        ]);

                    /* Creating Policy Acknowledgement tokens for users */
                    foreach ($campaign->policies as $key => $policy) {
                        $token = encrypt($campaign->id.'-'.$policy->id.'-'.$campaignGroupUser->id);

                        CampaignAcknowledgment::create([
                            'campaign_id' => $campaign->id,
                            'policy_id' => $policy->id,
                            'user_id' => $campaignGroupUser->id,
                            'token' => $token,
                        ]);
                    }
                }
            }

            return $campaign;
        });

        if (!$newCampaign) {
            return response()->json([
                'success' => false,
                'message' => 'Oops something went wrong.',
            ]);
        }

        //sending campaigns  email right away without waiting for CRON
        // Artisan::call('schedule:run');

        // executing shell script without waiting for it
        if(env('PHP_CONFLICT') && env('PHP8_BINARY')){
            $cmd= 'cd ' . base_path() . ' && '.env('PHP8_BINARY').' artisan tenants:run schedule:run ' . "> /dev/null 2>/dev/null &";
        }
        else{
            $cmd= 'cd ' . base_path() . ' && php artisan tenants:run schedule:run ' . "> /dev/null 2>/dev/null &";
        }
        shell_exec($cmd);

        //For dublicate campaign response
        if ($request->duplicate_campaign_form) {
            Log::info('User has duplicated a campaign.', ['campaign_id' => $newCampaign->id]);
            return redirect()->back()->with([
                'message' => 'Campaign duplicated successfully.',
                'data' => $newCampaign
            ]);
        }

        //for add campaign response
        Log::info('User has created a campaign.', ['campaign_id' => $newCampaign->id]);
        return redirect()->back()->with([
            'success' => 'Campaign added successfully.',
            'data' => $newCampaign
        ]);
    }

    public function show(Request $request, $campaignId)
    {
        $app_timezone = GlobalSetting::query()->first('timezone')->timezone;
        $campaign = Campaign::withoutGlobalScopes()->with(['acknowledgements', 'groups', 'policies'])->findOrFail($campaignId);
        $acknowledgements = $campaign->acknowledgements;
        $campaignActivities = $campaign->activities;
        $emailSentSuccessOld = $campaignActivities->where('activity', 'Email Sent on Campaign start')->count();
        $emailSentSuccessNew = $campaignActivities->Where('activity', 'Email Sent on new user')->count();
        $emailSentSuccess = $emailSentSuccessOld + $emailSentSuccessNew;
        $emailSentFailaures = $campaignActivities->where('type', 'email-sent-error')->count();
        $totalEmailSent = ($emailSentSuccess + $emailSentFailaures);

        //checking if user has completed all the policy to acknowledged
        $totalAcknowledgements = $acknowledgements->groupBy('user_id')->count();
        $pendingAcknowledgements = $acknowledgements->where('status', 'pending')->groupBy('user_id')->count();
        $completedAcknowledgements = $totalAcknowledgements - $pendingAcknowledgements;
        $completedAcknowledgementsPercentage = ($completedAcknowledgements && $totalAcknowledgements) ? round($completedAcknowledgements * 100 / $totalAcknowledgements) : 0;

        // Data for Campaign Timeline
        $campaignTimeline = [];

        foreach ($campaignActivities as $campaignActivity) {
            $itemContent =
            "<div class='timline-item-content'>".
            $campaignActivity->created_at->format('F jS, Y H:i:s').
            '

                <br>event: '.$campaignActivity->type.'<br>
                email: <b>'.$campaignActivity->user->email.'</b>
            </div>
            ';
            // Item ClassNames
            switch ($campaignActivity->type) {
                case 'email-sent':
                    $itemClass = $campaignActivity->type.' '.'bg-success';
                    break;
                case 'policy-acknowledged':
                    $itemClass = $campaignActivity->type.' '.'bg-warning';
                    break;
                case 'email-sent-error':
                    $itemClass = $campaignActivity->type.' '.'bg-danger';
                    break;
                case 'clicked-link':
                    $itemClass = $campaignActivity->type.' '.'bg-primary';
                    break;
                default:
                    $itemClass = $campaignActivity->type.' '.'bg-secondary';
            }

            $campaignTimeline[] = json_encode([
                'id' => $campaignActivity->id,
                'title' => $itemContent,
                // 'start' => $campaignActivity->created_at->format('Y-m-d H:i:s'),
                'start' => date('Y-m-d H:i:s', strtotime($campaignActivity->created_at)),
                'className' => $itemClass,
            ]);
        }

        $campaign['launch_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $campaign->launch_date, 'UTC')->setTimezone($app_timezone);
        $campaign['due_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $campaign->due_date, 'UTC')->setTimezone($app_timezone);

        return Inertia::render('policy-management/campaign-show-page/CampaignShowPage', [
            'campaignId' => $campaignId,
            'campaign' => $campaign,
            'totalAcknowledgements' => $totalAcknowledgements,
            'completedAcknowledgements' => $completedAcknowledgements,
            'completedAcknowledgementsPercentage' => $completedAcknowledgementsPercentage,
            'totalEmailSent' => $totalEmailSent,
            'emailSentSuccess' => $emailSentSuccess,
            'emailSentFailaures' => $emailSentFailaures,
            'campaignTimeline' => $campaignTimeline
        ]);
    }

    /**
     * Deling the campaigns.
     **/
    public function delete(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $deleted = $campaign->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Oops somthing went wrong, please try again'
            ]);
        }
        Log::info('User has deleted a campaign.', ['campaign_id' => $id]);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Campaign deleted successfully.'
        ]);
    }

    /**
     * Sends policy acknowledgement reminder to campaign users.
     * **/
    public function sendUsersReminder(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $nowDateTime = new \DateTime('now', new \DateTimeZone($campaign->timezone));
        $campaignLaunchDate = new \DateTime($campaign->launch_date, new \DateTimeZone($campaign->timezone));

        if ($campaignLaunchDate->format('Y-m-d H:i:sP') > $nowDateTime->format('Y-m-d H:i:sP')) {
            return redirect()->back()->withError("Can't send reminder to a campaign which has not been started yet.");
        }

        $acknowledgements = $campaign->acknowledgements->where('status', 'pending');

        $acknowledgementGroups = $acknowledgements->groupBy('user_id');

        foreach ($acknowledgementGroups as $index => $acknowledgementGroup) {
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
            } catch (\Exception $exception) {
                CampaignActivity::create([
                    'campaign_id' => $campaign->id,
                    'activity' => 'Error Sending Email',
                    'type' => 'email-sent-error',
                    'user_id' => $user->id,
                ]);

                return redirect()->back()->with(['exception' => 'Failed to process request. Please check SMTP authentication connection.']);
            }
        }
        Log::info('User has sent policy acknowledgement reminder to users', ['campaign_id' => $id]);
        return redirect()->back()->with('success', 'Campaign Acknowledgment reminder emails sent to policy management users.');
    }

    public function getCampaignData(Request $request, $campaignId)
    {
        $campaign = Campaign::with('policies')->findOrFail($campaignId);

        return response()->json([
            'success' => true,
            'data' => $campaign,
        ]);
    }

    public function exportPdf(Request $request, $campaignId)
    {
        $campaign = Campaign::with('acknowledgements')->findOrFail($campaignId);

        $acknowledgements = $campaign->acknowledgements;
        $campaignActivities = $campaign->activities;
        $emailSentSuccessOld = $campaignActivities->where('activity', 'Email Sent on Campaign start')->count();
        $emailSentSuccessNew = $campaignActivities->Where('activity', 'Email Sent on new user')->count();
        $emailSentSuccess = $emailSentSuccessOld + $emailSentSuccessNew;
        $emailSentFailaures = $campaignActivities->where('type', 'email-sent-error')->count();
        $totalEmailSent = ($emailSentSuccess + $emailSentFailaures);
        $totalAcknowledgements = $acknowledgements->groupBy('user_id')->count();
        $pendingAcknowledgements = $acknowledgements->where('status', 'pending')->groupBy('user_id')->count();
        $completedAcknowledgements = $totalAcknowledgements - $pendingAcknowledgements;
        $completedAcknowledgementsPercentage = ($completedAcknowledgements && $totalAcknowledgements) ? round($completedAcknowledgements * 100 / $totalAcknowledgements) : 0;
        $data = [
            'campaign' => $campaign,
            'timezone' => $this->appTimezone()[$campaign->timezone],
            'acknowledgements' => $acknowledgements,
            'emailSentSuccess' => $emailSentSuccess,
            'emailSentFailaures' => $emailSentFailaures,
            'totalEmailSent' => $totalEmailSent,
            'totalAcknowledgements' => $totalAcknowledgements,
            'completedAcknowledgements' => $completedAcknowledgements,
            'completedAcknowledgementsPercentage' => $completedAcknowledgementsPercentage,
        ];

        // return view(  $this->viewBasePath.'campaign-pdf-export', $data   );

        $pdf = \PDF::loadView($this->viewBasePath.'campaign-pdf-export', $data);
        $pdf->setOptions([
            'enable-local-file-access' => true,
            'enable-javascript' => true,
            'javascript-delay' => 3000,
            'enable-smart-shrinking' => true,
            'no-stop-slow-scripts' => true,
            'header-center' => 'Note: This is a system generated report',
            'footer-center' => 'Policy Management - Campaign Report',
            'footer-left' => 'Confidential',
            'footer-right' => '[page]',
            'debug-javascript' => true,
        ]);

        Log::info('User has downloaded a campaign dashboard report as pdf.', ['campaign_id' => $campaignId]);

        return $pdf->download('campaign-dashboard-report.pdf');
    }

    public function exportCsv(Request $request, $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        Log::info('User has downloaded a campaign dashboard report as csv.', ['campaign_id' => $campaignId]);

        return Excel::download(new usersStatusExport($campaign), 'campaign-users-status.csv');
    }

    public function getCampaignCreateData(Request $request)
    {
        $policies = Policy::query()->get();
        $groups = Group::query()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'policies' => $policies,
                'groups' => $groups
            ]
        ]);
    }
}
