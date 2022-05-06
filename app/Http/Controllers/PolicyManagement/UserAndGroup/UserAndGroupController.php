<?php

namespace App\Http\Controllers\PolicyManagement\UserAndGroup;

use App\Http\Controllers\Controller;
use App\Mail\PolicyManagement\Acknowledgement as AcknowledgementMail;
use App\Models\PolicyManagement\Campaign\Campaign;
use App\Models\PolicyManagement\Campaign\CampaignAcknowledgment;
use App\Models\PolicyManagement\Campaign\CampaignAcknowledgmentUserToken;
use App\Models\PolicyManagement\Campaign\CampaignActivity;
use App\Models\PolicyManagement\Group\Group;
use App\Models\PolicyManagement\Group\GroupUser;
use App\Models\PolicyManagement\User;
use App\Http\Resources\PolicyManagement\GroupListResource;
use App\Rules\common\UniqueWithinDataScope;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UserAndGroupController extends Controller
{
    // protected $viewBasePath = 'policy-management.users-and-groups';

    public function index()
    {
        // return view($this->viewBasePath.'.index');
        return Inertia::render('policy-management/users-and-groups/UsersAndGroups');
    }

    /****
     * Creating new groups
     */
    public function addGroup(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required', new UniqueWithinDataScope(new Group, 'name')],
        ]);

        if(count($request->users['groupsData']) === 0){
            $validator->getMessageBag()->add('usersRequired', 'Users are required to create a group.');
            return redirect()->back()->withErrors($validator);
        }

        $groupCreated = DB::transaction(function () use ($request) {
            $groupCreated = Group::create([
                'name' => $request->name,
            ]);

            ///Add all group users
            if (count($request->users['groupsData']) > 0) {
                foreach ($request->users['groupsData'] as $eachDataKey => $eachUserData) {
                    $firstName = $eachUserData['user_first_name'];
                    $lastName = $eachUserData['user_last_name'];
                    $email = $eachUserData['user_email'];

                    if ($firstName && $lastName && $email) {
                        /*Creating user in users template section*/
                        User::firstOrCreate(
                            ['email' => $email],
                            [
                                'first_name' => $firstName, 'last_name' => $lastName
                            ]
                        );

                        /* Creating Group Users */
                        $groupCreated->users()->firstOrCreate(
                            ['email' => $email],
                            [
                                'first_name' => $firstName, 'last_name' => $lastName
                            ]
                        );
                    }
                }
            }
            return $groupCreated;
        });

        if (!$groupCreated) {
            // return response()->json([
            //     'success' => false,
            // ]);
            return redirect()->back()->withErrors('Unable to added group');
        }
        Log::info('User has created a group.', ['group_id' => $groupCreated->id]);
        return back()->withSuccess('Group added successfully');
    }

    /***
     * @return get group json data
     */

    public function getGroupsJsonData(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;
        $search = $request->search ?? "";
        $sortingRequest = $request->order;
        $sortingColumn = 'id';
        $sortingOrder = 'desc';

        // // Getting sorting order
        // $sortingOrder = $sortingRequest[0]['dir']??"ASC";

        // // Getting sorting column
        // if ($sortingRequest[0]['column'] == 0) {
        //     $sortingColumn = 'name';
        // } elseif ($sortingRequest[0]['column'] == 2) {
        //     $sortingColumn = 'status';
        // } elseif ($sortingRequest[0]['column'] == 4) {
        //     $sortingColumn = 'created_at';
        // } elseif ($sortingRequest[0]['column'] == 5) {
        //     $sortingColumn = 'updated_at';
        // }

        $groupQuery = Group::with('users')->where(function ($query) use ($search) {
            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }
        })->orderBy($sortingColumn, $sortingOrder);
        $count = $groupQuery->count();
        // $groups = $groupQuery->offset($start)->take($length)->get();
        $groups = $groupQuery->offset($start)->take($length)->paginate($request->per_page ?? 10);
        $render = [];

        foreach ($groups as $group) {
            $status = '';

            if ($group->status == 'active') {
                $status = '<span class="badge bg-info">Active</span>';
            } elseif ($group->status == 'disabled') {
                $status = '<span class="badge bg-warning">Disabled</span>';
            }

            $render[] = [
                $group->name,
                $status,
                $group->users->count(),
                date('j M Y', strtotime($group->created_at)),
                date('j M Y', strtotime($group->updated_at)),
                $group->id
            ];
        }

        $groups->setCollection(collect($render));

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $groups,
        ]);
    }

    // public function getGroupsJsonData(Request $request)
    // {
    //     $start = $request->start;
    //     $length = $request->length;
    //     $draw = $request->draw;
    //     $search = $request->search;
    //     $sortingRequest = $request->order;
    //     $sortingColumn = 'id';
    //     $sortingOrder = 'desc';

    //     // Getting sorting order
    //     $sortingOrder = $sortingRequest[0]['dir'];

    //     // Getting sorting column
    //     if ($sortingRequest[0]['column'] == 0) {
    //         $sortingColumn = 'name';
    //     } elseif ($sortingRequest[0]['column'] == 2) {
    //         $sortingColumn = 'status';
    //     } elseif ($sortingRequest[0]['column'] == 4) {
    //         $sortingColumn = 'created_at';
    //     } elseif ($sortingRequest[0]['column'] == 5) {
    //         $sortingColumn = 'updated_at';
    //     }

    //     $groupQuery = Group::with('users')->where(function ($query) use ($search) {
    //         if ($search) {
    //             $query->where('name', 'like', '%'.$search.'%');
    //         }
    //     })->orderBy($sortingColumn, $sortingOrder);
    //     $count = $groupQuery->count();
    //     $groups = $groupQuery->offset($start)->take($length)->get();
    //     $render = [];

    //     foreach ($groups as $group) {
    //         $actions = "
    //         <div class='btn-group'>
    //             <a class='edit-group-action btn btn-info btn-xs waves-effect waves-light' data-group-id='".$group->id."' data-form-action-url='".route('policy-management.users-and-groups.groups.update', $group->id)."' href='".route('policy-management.users-and-groups.groups.edit', $group->id)."'>
    //                 <i class='fe-edit'></i>
    //             </a>
    //         ";
    //         $actions .= "<a class='btn btn-danger btn-xs waves-effect waves-light delete' href='".route('policy-management.users-and-groups.groups.delete', $group->id)."'><i class='fe-trash-2'></i></a>";
    //         $actions .= ' </div>';

    //         $status = '';

    //         if ($group->status == 'active') {
    //             $status = '<span class="badge bg-info">Active</span>';
    //         } elseif ($group->status == 'disabled') {
    //             $status = '<span class="badge bg-warning">Disabled</span>';
    //         }

    //         $render[] = [
    //             $group->name,
    //             $status,
    //             $group->users->count(),
    //             date('j M Y', strtotime($group->created_at)),
    //             date('j M Y', strtotime($group->updated_at)),
    //             $actions,
    //         ];
    //     }

    //     return response()->json([
    //         'draw' => $draw,
    //         'recordsTotal' => $count,
    //         'recordsFiltered' => $count,
    //         'data' => $render,
    //     ]);
    // }

    public function getGroupEditData(Request $request, $id)
    {
        $group = Group::with('users')->findOrFail($id);
        $data = [
            'group' => $group,
            'users' => $group->users,
        ];

        return $data;
    }

    /**
     *
     * @return list of groups
     */
    public function getGroupList(Request $request)
    {
        $groups = Group::all();

        return GroupListResource::collection($groups)->additional(['success' => true]);
    }

    public function getGroupUsersList(Request $request)
    {
        $groups = GroupUser::all();
        // return GroupListResource::collection($groups)->additional(['success' => true]);
        return $groups;
    }

    /***
     * frontend validation for group name taken
     */
    public function checkGroupNameTaken(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $group = Group::where(function ($query) use ($request, $id) {
            $query->where('name', $request->name);

            if ($id) {
                $query->where('id', '!=', $id);
            }
        })->first();

        if ($group) {
            return 'false';
        }

        return 'true';
    }

    /****
     * Updating the groups
     */
    public function updateGroup(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                new UniqueWithinDataScope(new Group, 'name', $id)
            ]
        ]);

        $group = Group::findOrFail($id);
        $userEmails = [];
        foreach ($request->users['groupsData'] as $eachData) {
            $userEmails[] = $eachData['user_email'];
        }
        $groupUpdated = DB::transaction(function () use ($request, $group, $id, $userEmails) {
            //Getting previous group users
            $beforeUpdateGroupUsers = $group->users->pluck('email')->toArray();

            //Group user delete if all user get deleted from group
            if (count($userEmails) > 0) {
                GroupUser::where('group_id', $id)->delete();
            } else {
                //Checking if use get delete from previous group
                $arraydiff = (array_diff($beforeUpdateGroupUsers, $userEmails));

                if ($arraydiff) {
                    GroupUser::where('group_id', $id)->wherein('email', $arraydiff)->delete();
                }
            }

            $group->name = $request->name;
            $groupUpdated = $group->update();
            $newlyCreatedUsers = [];

            if (count($request->users['groupsData']) > 0) {
                foreach ($request->users['groupsData'] as $eachDataKey => $eachUserData) {
                    $firstName = $eachUserData['user_first_name'];
                    $lastName = $eachUserData['user_last_name'];
                    $email = $eachUserData['user_email'];

                    if ($firstName && $lastName && $email) {
                        /*Creating user in users template section*/
                        User::updateOrCreate(
                            ['email' => $email],
                            [
                                'first_name' => $firstName, 'last_name' => $lastName
                            ]
                        );

                        /* Creating Group Users */
                        $groupUser = $group->users()->updateOrCreate(
                            ['email' => $email],
                            [
                                'first_name' => $firstName, 'last_name' => $lastName
                            ]
                        );

                        /*WHEN NEWLY CREATED USERS PUSHING TO NEWLY ADDED USERS COLLECTION*/
                        if ($groupUser->wasRecentlyCreated) {
                            $newlyCreatedUsers[] = $groupUser;
                        }
                    }
                }
            }

            // Creating acknowledement for new group in all related campaign

            /*
             *  CASE NEW USER ADDED TO GROUPS
             */
            if (count($newlyCreatedUsers) > 0) {
                $acknowledgements = [];
                $campaignAcknowledgmentUserTokens = [];

                // Updating campaigns on new user added to group
                foreach ($group->campaigns as $key => $campaign) {
                    $nowDateTime = new \DateTime('now', new \DateTimeZone($campaign->timezone));
                    $campaignLaunchDate = new \DateTime($campaign->launch_date, new \DateTimeZone($campaign->timezone));
                    $campaignDueDate = new \DateTime($campaign->due_date, new \DateTimeZone($campaign->timezone));

                    /*
                     *  Skip
                     * Case when campaign auto-enroll is no and  campaign already started yet
                     */
                    if ($campaign->auto_enroll_users == 'no' && $nowDateTime >= $campaignLaunchDate) {
                        break;
                    }

                    /*
                     *  Skip
                     * Case when campaign auto-enroll is yes and dues date has been passed
                     */
                    if ($campaign->auto_enroll_users == 'yes' && $nowDateTime >= $campaignDueDate) {
                        break;
                    }

                    // updating campaign status
                    $campaign->status = 'active';
                    $campaign->update();

                    // for all new user added to group creating acknowledgment tokens
                    foreach ($newlyCreatedUsers as $user) {
                        /*Getting campaign group quering the group id in campaign groups table*/
                        $campaignGroup = $campaign->groups()->where('group_id', $group->id)->first();

                        if (!$campaignGroup) {
                            break;
                        }

                        /* Adding new user to campaign group users table */
                        $campaignGroupUser = $campaignGroup->users()->firstOrCreate([
                            'email' => $user->email,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                        ]);

                        // Breaking of loop when new campaign group user is created
                        if (!$campaignGroupUser->wasRecentlyCreated) {
                            break;
                        }

                        // Creating acknowledgment token for new users
                        $acknowledgmentUserToken = CampaignAcknowledgmentUserToken::create([
                            'campaign_id' => $campaign->id,
                            'user_id' => $campaignGroupUser->id,
                            'token' => encrypt($campaign->id . '-' . $campaignGroupUser->id . date('r', time())),
                        ]);

                        foreach ($campaign->policies as $policy) {
                            $token = encrypt($campaign->id . '-' . $policy->id . '-' . $campaignGroupUser->id);

                            CampaignAcknowledgment::create([
                                'campaign_id' => $campaign->id,
                                'policy_id' => $policy->id,
                                'user_id' => $campaignGroupUser->id,
                                'token' => $token,
                            ]);
                        }

                        // Sending acknowledgement email
                        $user = $acknowledgmentUserToken->user;

                        $CampAcknowledgements = CampaignAcknowledgment::where('campaign_id', $acknowledgmentUserToken->campaign_id)
                            ->where('user_id', $user->id)->get();
                        $acknowledgementGroup = $CampAcknowledgements->groupBy('user_id')->first();

                        try {
                            Mail::to($user->email)->send(new AcknowledgementMail($acknowledgmentUserToken, $campaign, $acknowledgementGroup, $user));

                            // When email sent successfully
                            CampaignActivity::create([
                                'campaign_id' => $campaign->id,
                                'activity' => 'Email Sent on new user',
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

            return $groupUpdated;
        });

        if (!$groupUpdated) {
            return redirect()->back()->withErrors('Oops something went wrong!');
        }
        Log::info('User has updated a group.', ['group_id' => $id]);
        return redirect()->back()->withSuccess('Group updated successfully.');
    }

    public function deleteGroup(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $groupDeleted = $group->delete();
        Log::info('User has deleted a group.', ['group_id' => $id]);
        return redirect()->back()->withSuccess('Group deleted successfully.');
    }
}
