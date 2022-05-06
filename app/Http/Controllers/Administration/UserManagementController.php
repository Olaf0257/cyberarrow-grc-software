<?php

namespace App\Http\Controllers\Administration;

use Auth;
use Notification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Utils\DepartmentTree;
use App\Utils\RegularFunctions;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\UserManagement\LdapUser;
use App\Rules\Admin\Auth\StrongPassword;
use App\Models\Compliance\ProjectControl;
use App\Models\UserManagement\VerifyUser;
use App\Models\GlobalSettings\LdapSetting;
use App\Models\UserManagement\AdminDepartment;
use App\Notifications\CreateNewUserNotification;
use App\Notifications\CreateNewSsoUserNotification;
use App\Models\Administration\OrganizationManagement\Department;
use App\Models\Administration\OrganizationManagement\Organization;

class UserManagementController extends Controller
{
    protected $loggedUser;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->loggedUser = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function view()
    {
        return inertia('user-management/components/UserList');
    }

    public function create()
    {
        Log::info('User is attempting to create a new admin account');
        $admin = new Admin();
        $admin->roles = [];
        $roles = RegularFunctions::getAllRoles();
        $departmentTree = new DepartmentTree();
        $departmentTreeData = $departmentTree->getTreeData();

        return inertia('user-management/components/UserCreatePage', compact('roles', 'departmentTree', 'departmentTreeData'));
    }

    public function getLdapUserInfo(Request $request)
    {
        $ldapSetting = LdapSetting::first();
        if (is_null($ldapSetting)) {
            return response()->json([
                'success' => false,
            ]);
        }

        $ldapUser = LdapUser::where($ldapSetting->map_email_to, $request->email)->first();

        if (is_null($ldapUser)) {
            return response()->json([
                'success' => false,
            ]);
        }

        $ldapUserInfo = [
            'firstName' => $ldapUser[$ldapSetting->map_first_name_to] ? $ldapUser[$ldapSetting->map_first_name_to][0] : '',
            'lastName' => $ldapUser[$ldapSetting->map_last_name_to] ? $ldapUser[$ldapSetting->map_last_name_to][0] : '',
            'email' => $ldapUser[$ldapSetting->map_email_to] ? $ldapUser[$ldapSetting->map_email_to][0] : '',
            'contactNumber' => $ldapUser[$ldapSetting->map_contact_number_to] ? $ldapUser[$ldapSetting->map_contact_number_to][0] : '',
        ];

        return response()->json([
            'success' => true,
            'data' => $ldapUserInfo,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'auth_method' => 'required|in:Manual,SSO,LDAP',
            'first_name' => 'required|string|max:35',
            'last_name' => 'required|max:35',
            'department_id' => ['required', 'integer', function ($attribute, $value, $fail) use ($request) {
                $isValid = false;
                $organization = Organization::first();

                if ($organization) {

                    if ($request->department_id != 0) {
                        $departCount = Department::whereIn('id', [$request->department_id])->count();

                        if ($departCount > 0) {
                            $isValid = true;
                        }
                    } else {
                        $isValid =  true;
                    }
                }

                if (!$isValid) {
                    $fail('Selected department does not exists.');
                }
            }],
            'email' => 'required|email|unique:admins',
            'contact_number_country_code' => 'nullable',
            'contact_number' => ['nullable', 'numeric', 'digits_between:9,15', function ($attribute, $value, $fail) use ($request) {
                $exist = Admin::where('contact_number_country_code', $request->contact_number_country_code)->where('contact_number', $value)->first();

                if ($exist) {
                    $fail('Phone/Contact number already exists.');
                }
            }],
            'roles' => 'required|array',
            'roles.*' => [
                Rule::in(['Global Admin', 'Auditor', 'Contributor', 'Compliance Administrator', 'Policy Administrator', 'Risk Administrator', 'Third Party Risk Administrator']),
            ],
        ]);

        $input = $request->toArray();
        $departmentId = $input['department_id'] > 0 ? $input['department_id'] : null;

        /* Making the status active when auth method is LDAP OR SSO */
        if ($input['auth_method'] == 'LDAP' || $input['auth_method'] == 'SSO') {
            $input['status'] = 'active';
        }

        $admin = Admin::create($input);

        $organization = Organization::first();

        /* Creating departments */
        $department = new AdminDepartment([
            'admin_id' => $admin->id,
            'organization_id' => $organization->id,
            'department_id' => $departmentId
        ]);
        $admin->department()->save($department);

        if ($request->roles) {
            if (in_array('Global Admin', $input['roles'])) {
                $admin->assignRole(['Global Admin']);
            } else {
                $admin->assignRole($input['roles']);
            }
        }

        if ($admin->auth_method == 'Manual') {
            // Creating email verification token
            VerifyUser::create([
                'user_id' => $admin->id,
                'token' => Str::random(100),
            ]);

            Notification::route('mail', $input['email'])->notify(new CreateNewUserNotification($admin));
        } else {
            Notification::route('mail', $input['email'])->notify(new CreateNewSsoUserNotification($admin));
        }
        Log::info('User has created a new admin account', [
            'admin_id' => $admin->id
        ]);

        return redirect(route('admin-user-management-view'))->with('success', 'User created successfully.');
    }

    public function edit(Request $request, Admin $admin)
    {
        if(auth()->user()->id !== $admin->id && !auth()->user()->hasRole('Global Admin')){
            abort(403);
        }
        $admin['created_date'] = date('d M, Y', strtotime($admin->created_at));
        $admin['updated_date'] = date('d M, Y', strtotime($admin->updated_at));
        $admin['last_login'] = isset($admin->last_login) ? date('d M, Y H:s A', strtotime($admin->last_login)) : null;
        $departmentId = $admin->department->department_id ?? 0;
        $hasMFA = $admin->hasTwoFactorEnabled();
        $loggedInUser = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : null;
        $isGlobalAdmin = $loggedInUser ? $loggedInUser->hasRole('Global Admin') : false;
        $assignedRoles = [];
        foreach ($admin->roles as $role) {
            array_push($assignedRoles, $role->name);
        }
        $admin->roles = $assignedRoles;
        $roles = RegularFunctions::getAllRoles();
        $departmentTree = new DepartmentTree();
        $departmentTreeData = $departmentTree->getTreeData();

        return inertia('user-management/components/UserEditPage', compact('admin', 'departmentId', 'hasMFA', 'loggedInUser', 'isGlobalAdmin', 'roles', 'departmentTreeData'));
    }

    public function update(Request $request, Admin $admin)
    {
        if(auth()->user()->id !== $admin->id && !auth()->user()->hasRole('Global Admin')){
            abort(403);
        }
        $this->validate($request, [
            'first_name' => 'required|max:35',
            'last_name' => 'required|max:35',
            'department_id' => ['required', 'integer', function ($attribute, $value, $fail) use ($request) {
                $isValid = false;
                $organization = Organization::first();

                if ($organization) {
                    if ($request->department_id != 0) {
                        $departCount = Department::whereIn('id', [$request->department_id])->count();

                        if ($departCount > 0) {
                            $isValid = true;
                        }
                    } else {
                        $isValid =  true;
                    }
                }

                if (!$isValid) {
                    $fail('Selected department does not exists.');
                }
            }],
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'contact_number_country_code' => 'nullable',
            'contact_number' => 'nullable|numeric|digits_between:9,15',
            'roles' => [
                Rule::requiredIf(function () use ($request, $admin) {
                    return $this->loggedUser->hasRole('Global Admin');
                }),
            ],
        ]);

        $input = $request->all();

        $departmentId = $input['department_id'] > 0 ? $input['department_id'] : null;

        $updated = $admin->fill($input)->save();

        /* Updating department */
        if ($admin->department === null) {
            $organization = Organization::first();
            $department = new AdminDepartment(['organization_id' => $organization->id, 'department_id' => $departmentId]);
            $admin->department()->save($department);
        } else {
            $admin->department()->update([
                'department_id' => $departmentId
            ]);
        }

        if ($this->loggedUser->hasRole('Global Admin')) {
            if ($request->roles) {
                if (in_array('Global Admin', $input['roles'])) {
                    $admin->syncRoles(['Global Admin']);
                } else {
                    $admin->syncRoles($input['roles']);
                }
            }
        }
        Log::info('User has updated an admin account', [
            'admin_id' => $admin->id
        ]);
        return back()->with('success', 'User profile updated successfully.');
    }

    public function delete(Admin $admin)
    {
        if($admin->status === 'unverified') {
            $admin->delete();

            Log::info('User has deleted an admin account', [
                'admin_id' => $admin->id
            ]);
            return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
        }
        return response()->json(['success' => false, 'message' => 'Cannot delete verified user!']);
    }

    public function makeActive(Request $request, Admin $admin)
    {
        $admin->status = 'active';
        $admin->save();
        Log::info('User has activated an admin account', [
            'admin_id' => $admin->id
        ]);
        return response()->json(['sucess' => true, 'message' => 'User reactivated successfully!']);
    }

    public function makeDisable(Request $request, Admin $admin)
    {
        if ($this->loggedUser->id == $admin->id) {
            return RegularFunctions::accessDeniedResponse();
        }

        $admin->status = 'disabled';
        $admin->save();
        Log::info('User has disabled an admin account', [
            'admin_id' => $admin->id
        ]);
        return response()->json(['success' => true]);
    }

    public function transferAssignments(Request $request, Admin $admin)
    {
        $request->validate([
            'transfer_to' => 'required|numeric',
        ]);

        if ($request->transfer_to) {
            $approverAssignments = ProjectControl::where('approver', $admin->id)->get();
            $responsibleAssignments = ProjectControl::where('responsible', $admin->id)->get();

            if (count($approverAssignments) > 0 || count($responsibleAssignments) > 0) {
                $assignments = $approverAssignments->merge($responsibleAssignments);

                if ($assignments->where('responsible', $request->transfer_to)->count() > 0 || $assignments->where('approver', $request->transfer_to)->count() > 0) {
                    return response()->json(['success' => false, 'message' => 'This user has already been assigned for controls.']);
                }

                if (count($approverAssignments) > 0) {
                    foreach ($approverAssignments as $approverAssignment) {
                        $approverAssignment->approver = $request->transfer_to;

                        $approverAssignment->update();
                    }
                }

                if (count($responsibleAssignments) > 0) {
                    foreach ($responsibleAssignments as $assignment) {
                        $assignment->responsible = $request->transfer_to;

                        $assignment->update();
                    }
                }

                return response()->json(['success' => true, 'message' => 'Ok']);
            } else {
                return response()->json(['success' => false, 'message' => 'This user has not been assigned to any tasks']);
            }
        }
    }

    public function getUserProjectAssignments(Request $request, Admin $admin)
    {
        $projectControls = ProjectControl::where('responsible', $admin->id)->orWhere('approver', $admin->id)->count();

        return response()->json(['status' => 'ok', 'data' => $projectControls]);
    }

    public function getAssignmentTransferableUsers(Request $request, Admin $admin)
    {
        $taskContributors = Admin::with('roles')->where('status', 'active')->where('id', '!=', $admin->id)->get();

        $taskContributors = $taskContributors->filter(function ($contributor) {
            return $contributor->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Contributor']);
        });

        return response()->json(['success' => true, 'data' => $taskContributors]);
    }

    //updating password from user profile page
    public function updatePassword(Request $request, Admin $admin)
    {
        if(auth()->user()->id !== $admin->id && !auth()->user()->hasRole('Global Admin')){
            abort(403);
        }
        if ($admin->auth_method != 'Manual') {
            exit;
        }

        $request->validate([
            'current_password' => [
                Rule::requiredIf(function () use ($admin) {
                    return $admin->id == $this->loggedUser->id;
                }),
                function ($attribute, $value, $fail) use ($admin) {
                    if (!Hash::check($value, $admin->password)) {
                        $fail('Current password is incorrect.');
                    }
                },
            ],
            'new_password' => ['required', 'confirmed', new StrongPassword()],
        ], [
            'new_password.required' => 'The new password field is required',
        ]);

        $admin->password = bcrypt($request->new_password);
        $admin->update();
        Log::info('User has changed the password for an admin account', [
            'admin_id' => $admin->id
        ]);

        return back()->with('success', 'Password successfully updated.');
    }

    public function resendEmailVerificationLink(Admin $admin)
    {
        $verifyUser = $admin->verifyUser;
        $verifyUser->token = Str::random(100);
        $verifyUser->update();

        Notification::route('mail', $admin->email)->notify(new CreateNewUserNotification($admin));

        Log::info('User has resent verification email to an admin account', [
            'admin_id' => $admin->id
        ]);

        return back()->with('success', 'User email verification link has been resent successfully.');
    }

    public function getJsonData(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $draw = $request->draw;
        $keyword = $request->search['value'];

        $aminListQuery = Admin::when($request->search['value'] != null, function ($query) use ($keyword) {
            return $query->where(\DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', $keyword . '%')
                ->orWhere('email', 'LIKE', $keyword . '%')
                ->orWhere('contact_number', 'LIKE', $keyword . '%');
        });

        $count = $aminListQuery->count();
        $admins = $aminListQuery->offset($start)->take($length)->get();

        $render = [];

        foreach ($admins as $admin) {
            $assignedRoles = [];
            foreach ($admin->roles as $role) {
                array_push($assignedRoles, $role->name);
            }

            $roles = '';

            // user roles
            foreach ($assignedRoles as $assignedRole) {
                $roles .= "<span class='badge bg-soft-info text-info'> {$assignedRole}</span> ";
            }

            if ($admin->status == 'active') {
                $status = "<span class='badge bg-info'>Active</span>";
                $actionStatus = '';

                if ($this->loggedUser->id != $admin->id) {
                    $actionStatus = "<a class='dropdown-item disable-user' data-user-id='$admin->id' data-assignment-transferable-user-url='" . route('user.assignments-transferable-users', [$admin->id]) . "' data-user-project-assignments-url='" . route('user.project-assignments', [$admin->id]) . "' href='" . route('admin-user-management-make-disable', [$admin->id])
                        . "'
                        data-transfer-assignments-url='" . route('user.transfer-assignments', [$admin->id]) . "'>
                            <i class='mdi mdi-account-check me-2 text-muted font-18 vertical-middle'></i>Disable
                        </a>";
                }
            } elseif ($admin->status == 'unverified') {
                $status = "<span class='badge bg-warning'>Unverified</span>";
                $actionStatus = '';
            } else {
                $status = "<span class='badge bg-danger'>Disabled</span>";
                $actionStatus = "<a class='dropdown-item activate-user' href='" . route('admin-user-management-make-active', [$admin->id])
                    . "'><i class='mdi mdi-account-check me-2 text-muted font-18 vertical-middle'></i>Active</a>";
                $actionStatus .= "<a class='dropdown-item delete-user' href='" . route('admin-user-management-delete', [$admin->id])
                    . "'><i class='mdi mdi-delete-forever me-2 text-muted font-18 vertical-middle'></i>Delete</a>";
            }

            $action = "<div class='btn-group dropdown dropstart'>
                    <a href='javascript: void(0);' class='table-action-btn dropdown-toggle arrow-none btn btn-light btn-sm' data-toggle='dropdown'
                    aria-expanded='false'><i class='mdi mdi-dots-horizontal'></i></a><div class='dropdown-menu'>
                    <a class='dropdown-item' href='" . route('admin-user-management-edit', [$admin->id]) . "'><i class='mdi mdi-pencil me-2 text-muted font-18 vertical-middle'></i>Edit User</a>" .
                $actionStatus . '</div></div>';

            if ($admin->last_login) {
                $lastLogin = date('j M Y, H:i:s', strtotime($admin->last_login));
            } else {
                $lastLogin = '';
            }

            $organization = Organization::first();

            $department = "";

            if (!is_null($admin->department)) {
                /* admin departments table checking for departmen is set to top level*/
                if (is_null($admin->department->department_id)) {
                    $department =  $organization ? $organization->name . ' (Organization)' : '';
                } else {
                    $department = $admin->department->department ?  $admin->department->department->name : '';
                }
            }

            $render[] = [
                $admin->id,
                $admin->auth_method,
                $admin->first_name,
                $admin->last_name,
                $admin->email,
                $department,
                '(&nbsp;' . $admin->contact_number_country_code . '&nbsp;)&nbsp;' . $admin->contact_number,
                $roles,
                $status,
                date('j M Y', strtotime($admin->created_at)),
                date('j M Y', strtotime($admin->updated_at)),
                $lastLogin,
                $action,
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $render,
        ];

        echo json_encode($response);
    }

    public function getUsersDataReact(Request $request)
    {
        $page = $request->page ?? 1;
        $size = $request->per_page ?? 10;
        $keyword = $request->search ?? null;

        $admins = Admin::query()
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where(\DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', $keyword . '%')
                    ->orWhere('email', 'LIKE', $keyword . '%')
                    ->orWhere('contact_number', 'LIKE', $keyword . '%');
            });

        $count = $admins->count();
        $admins = $admins->with(['roles', 'department'])->skip(--$page * $size)->take($size)->paginate($size);

        foreach ($admins as $admin) {
            foreach ($admin->roles as $role) {
                $admin['role_names'] = $admin->roles;
            }

            $organization = Organization::first();

            if (!is_null($admin->department)) {
                /* admin departments table checking for department is set to top level*/
                if (is_null($admin->department->department_id)) {
                    $admin['department_name'] = $organization ? $organization->name . ' (Organization)' : '';
                } else {
                    $admin['department_name'] = $admin->department->department ? $admin->department->department->name : '';
                }
            }

            $admin['created_date'] = date('d M, Y', strtotime($admin->created_at));
            $admin['updated_date'] = date('d M, Y', strtotime($admin->updated_at));
            $admin['last_login'] = isset($admin->last_login) ? date('d M, Y H:s A', strtotime($admin->last_login)) : null;
        }

        return response()->json([
            'data' => $admins,
            'total' => $count,
        ], 200);
    }

    public function disableUser($id)
    {
        $admin = Admin::findOrFail($id);
        if ($this->loggedUser->id == $admin->id) {
            return RegularFunctions::accessDeniedResponse();
        }

        $admin->status = 'disabled';
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'User Disabled Successfully!'
        ]);
    }

    public function activateUser($id)
    {
        $admin = Admin::findOrFail($id);
        if ($this->loggedUser->id == $admin->id) {
            return RegularFunctions::accessDeniedResponse();
        }

        $admin->status = 'active';
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'User Activated Successfully!'
        ]);
    }
}
