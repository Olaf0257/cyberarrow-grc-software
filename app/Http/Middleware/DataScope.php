<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Administration\OrganizationManagement\Department;
use App\Models\Administration\OrganizationManagement\Organization;

class DataScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->has('data_scope')) {
            return response('Unauthorized.', 401);
        }

        $dataScope = explode('-', request('data_scope'));


        /* not a valid data format if data scope data array is not equal to Two*/
        if (count($dataScope) !== 2) {
            return response('Unauthorized.', 401);
        }

        $dataScopeOrganizationId = $dataScope[0];
        $dataScopeDepartmentId = $dataScope[1];

        /* checking data scope organization is valid */
        $organization = Organization::where('id', $dataScopeOrganizationId)->first();
        if (!$organization) {
            return response('Unauthorized.', 401);
        }

        if ($dataScopeDepartmentId != '0') {
            $department =  Department::where('id', $dataScopeDepartmentId)->first();

            if (!$department) {
                return response('Unauthorized.', 401);
            }
        }

        /* Getting the auth user department */
        $authUserDepart = auth()->user()->department;

        /* not allowing lower level user to access upper level data scope */
        if (!is_null($authUserDepart->department_id)) {
            $departmentIds = [];
            $departments = Department::where('id', $authUserDepart->department_id)->with(['departments'])->get();

            foreach ($departments as $key => $department) {
                $departmentIds[] = $department->id;

                $departmentIds = array_merge($departmentIds, $department->getAllChildDepartIds());
            }

            if (!in_array($dataScopeDepartmentId, $departmentIds)) {
                return response('Unauthorized.', 401);
            }
        }
        /*
            adding data scope attribut to request object
        */
        $request->request->add([
            'data_scope_org_id' => $dataScopeOrganizationId,
            'data_scope_depart_id' => ($dataScopeDepartmentId != '0') ? $dataScopeDepartmentId : Null
        ]);

        return $next($request);
    }
}
