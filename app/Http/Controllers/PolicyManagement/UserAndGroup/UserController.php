<?php

namespace App\Http\Controllers\PolicyManagement\UserAndGroup;

use App\Exports\PolicyManagement\userTemplate;
use App\Http\Controllers\Controller;
use App\Models\PolicyManagement\Group\Group;
use App\Models\PolicyManagement\Group\GroupUser;
use App\Models\PolicyManagement\User;
use App\Rules\common\UniqueWithinDataScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    protected $viewBasePath = 'policy-management.users-and-groups.user';

    public function create()
    {
        $user = new User();

        return view($this->viewBasePath.'.create', compact('user'));
    }

    public function getUsers(Request $request)
    {
        $sortingRequest = $request->order;
        $sortingColumn = 'first_name';
        $sortingOrder = 'asc';
        $searchKeyword = $request->search ?? null;

        // // Getting sorting order
        // $sortingOrder = $sortingRequest[0]['dir'];

        // // Getting sorting column
        // if ($sortingRequest[0]['column'] == 0) {
        //     $sortingColumn = 'first_name';
        // } elseif ($sortingRequest[0]['column'] == 1) {
        //     $sortingColumn = 'last_name';
        // } elseif ($sortingRequest[0]['column'] == 2) {
        //     $sortingColumn = 'email';
        // } elseif ($sortingRequest[0]['column'] == 4) {
        //     $sortingColumn = 'status';
        // } elseif ($sortingRequest[0]['column'] == 5) {
        //     $sortingColumn = 'created_at';
        // } elseif ($sortingRequest[0]['column'] == 6) {
        //     $sortingColumn = 'updated_at';
        // }

        $usersBaseQuery = User::when($searchKeyword != null, function ($query) use ($searchKeyword) {
            return $query->where(\DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', $searchKeyword.'%')
                    ->orWhere('email', 'LIKE', $searchKeyword.'%');
        })->orderBy($sortingColumn, $sortingOrder);
        $count = $usersBaseQuery->count();
        // $users = $usersBaseQuery->offset($start)->take($length)->get();
        $users = $usersBaseQuery->paginate($request->per_page?$request->per_page:10);

        $render = [];

        foreach ($users as $key => $user) {
            $groups = '';
            $status = '';
            if ($user->status == 'active') {
                $status = '<span class="badge bg-info">Active</span>';
            } elseif ($user->status == 'disabled') {
                $status = '<span class="badge bg-warning">Disabled</span>';
            }
            $render[] = [
                $user->id,
                $user->first_name,
                $user->last_name,
                $user->email,
                $groups,
                $status,
                date('j M Y', strtotime($user->created_at)),
                date('j M Y', strtotime($user->updated_at)),
                $user->status,
            ];
        }
        $users->setCollection(collect($render));

        return response()->json([
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $users,
        ]);
    }

    /***
     * Create
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                new UniqueWithinDataScope(new User, 'email')
            ],
            'first_name' => 'required|string',
            'last_name' => 'required',
        ]);

        $created = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        if (!$created) {
            return redirect()->back()->withErrors('Oops something went wrong');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $created,
            ]);
        }

        return redirect(route('policy-management.users-and-groups'))->with([
            'success' => 'User added successfully.',
            'activeTab' => 'users',
        ]);
    }

    /***
     * EDIT
     */
    public function edit(Request $request, $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /***
    * Update user
    */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                'email',
                new UniqueWithinDataScope(new User, 'email', $id)
            ]
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator, 'formErrors')
                        ->withInput();
        }

        $user = User::findOrFail($id);

        $updated = $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        return redirect()->back()->with([
            'success' => 'User updated successfully.',
            'activeTab' => 'users',
        ]);
    }

    /***
     * Disable user
     */
    public function disable(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'disabled',
        ]);
        return redirect()->back()->with([
            'success' => 'User disabled successfully.',
            'activeTab' => 'users',
        ]);
    }

    /***
     *  DELETE USER
     */
    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            
            GroupUser::where('email', $user->email)->delete();

            $user->delete();

            DB::commit();

            return redirect()->back()->withSuccess('User deleted successfully.');
        }catch (\Exception $e){
            DB::rollBack();
            return redirect()->back()->withErrors('Failed to delete user.');
        }
    }

    /***
     *  ACTIVATE USER
     *
     */
    public function activate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status' => 'active',
        ]);
        return redirect()->back()->with([
            'success' => 'User activated successfully.',
            'activeTab' => 'users',
        ]);
    }

    /***
     * checks the user if already exist for validation purpose
     */
    public function checkUserByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return 'true';
        }

        return 'false';
    }

    public function downloadCsvTemplate(Request $request)
    {
        return Excel::download(new userTemplate(), 'user-template.csv');
    }

    public function getLdapUsers(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;

        $render = [];
        $ldapUsers = [
        ];
        $count = 0;

        foreach ($ldapUsers as $key => $ldapUser) {
            $render[] = [];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $render,
        ]);
    }
}
