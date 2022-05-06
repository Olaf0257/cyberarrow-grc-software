<?php

namespace App\Http\Controllers\DataScope;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Administration\OrganizationManagement\Organization;
use App\Models\Administration\OrganizationManagement\Department;
use Illuminate\Support\Facades\Auth;

class DataScopeController extends Controller
{
    public function getTreeViewDropdownData(Request $request)
    {
        $authUser = $request->user();
        $userDepart = $authUser->department()->first();

        if (is_null($userDepart) || (isset($userDepart) && is_null($userDepart->department_id))) {
            $topNode = Organization::with(['departments' => function ($query) {
                $query->where('parent_id', 0);
            }])->first();
            $topNodeValue = $topNode->id.'-0';
            $organizationId = $topNode->id;
        } else {
            $topNode = Department::where('id', $userDepart->department_id)->with(['departments'])->first();
            $topNodeValue = $topNode->organization_id.'-'.$topNode->id;
            $organizationId = $topNode->organization_id;
        }

        /* WHEN EHRE IS NO ORGANIZATION */
        if (!$topNode) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $data = [
                'value' =>  $topNodeValue,
                'label' => $topNode->name
        ];


        $data = $this->buildTreeDepartments($topNode->departments, $data, $organizationId);

        return response()->json([
            'success' => true,
            'data' => [$data]
        ]);
    }

    /*
    * Creates department data in tree views
    */
    public function buildTreeDepartments($departments, $data, $organizationId)
    {
        foreach ($departments as $department) {
            $childData= [
                'value' => $organizationId.'-'.$department->id,
                'label' => $department->name
            ];

            if ($department->departments()->count() > 0) {
                $childData = self::buildTreeDepartments($department->departments, $childData, $organizationId);
            }

            $data['children'][] = $childData;
        }

        return $data;
    }

    /**
     * getting auth user details
    */
    public function getAuthUserDetails(Request $request)
    {
        $authUser = Auth::guard('admin')->user()->load('roles');


        return response()->json([
            'success' => true,
            'data' => [
                'user-details' => [
                    "auth_method" => $authUser->auth_method,
                    "first_name" => $authUser->first_name,
                    "last_name" => $authUser->last_name,
                    "email" => $authUser->email,
                    "is_sso_auth" => $authUser->is_sso_auth,
                    'avatar' => $authUser->avatar
                ],
                'user-roles' => $authUser->roles->pluck(['name'])
            ]
        ]);
    }
}
