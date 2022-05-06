<?php

namespace App\Http\Controllers\Administration\OrganizationManagement;

use App\Http\Controllers\Controller;
use App\Models\Administration\OrganizationManagement\Department;
use App\Models\UserManagement\Admin;
use App\Models\UserManagement\AdminDepartment;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DepartManagementController extends Controller
{
    public function index(Request $request, $organizationId)
    {
        $departments = Department::orderBy('sort_order', 'ASC')->where('organization_id', $organizationId)->get();

        return response()->json([
            'success' => true,
            'data' => $departments,
        ]);
    }

    /**
     * Updates the all departments.
     */
    public function saveNestedDepartment(Request $request, $organizationId)
    {
        $json = $request->nested_department_array;
        $decoded_json = json_decode($json, true) ?: [];
        $simplified_list = [];

        $this->recur1($decoded_json, $simplified_list);
        Log::info('User attempted to save nested departments');

        DB::beginTransaction();
        try {
            $info = [
                'success' => false,
            ];

            foreach ($simplified_list as $k => $v) {
                $department = Department::where('organization_id', $organizationId)->where('id', $v['id'])->first();
                $department->fill([
                    'parent_id' => $v['parent_id'],
                    'sort_order' => $v['sort_order'],
                ]);

                $department->save();
            }

            DB::commit();
            $info['success'] = true;
            Log::info('User updated nested departments');
        } catch (\Exception $e) {
            DB::rollback();
            Log::info('User failed to update nested departments');
            $info['success'] = false;
        }

        if ($info['success']) {
            return redirect()->back()->with([
                'success' => 'All departments updated successfully.',
                'activeTab' => 'organizations',
            ]);
        } else {
            return redirect()->back()->with([
                'error' => 'Something went wrong while updating...',
                'activeTab' => 'organizations',
            ]);
        }
    }

    public function recur1($nested_array = [], &$simplified_list = [])
    {
        static $counter = 0;

        foreach ($nested_array as $k => $v) {
            $sort_order = $k + 1;
            $simplified_list[] = [
                'id' => $v['id'],
                'parent_id' => 0,
                'sort_order' => $sort_order,
            ];

            if (!empty($v['children'])) {
                ++$counter;
                $this->recur2($v['children'], $simplified_list, $v['id']);
            }
        }
    }

    public function recur2($sub_nested_array = [], &$simplified_list = [], $parent_id = null)
    {
        static $counter = 0;

        foreach ($sub_nested_array as $k => $v) {
            $sort_order = $k + 1;
            $simplified_list[] = [
                'id' => $v['id'],
                'parent_id' => $parent_id,
                'sort_order' => $sort_order,
            ];

            if (!empty($v['children'])) {
                ++$counter;

                return $this->recur2($v['children'], $simplified_list, $v['id']);
            }
        }
    }


    public function store(Request $request, $organizationId)
    {
        $request->validate([
            'name' => 'required|max:255|unique:departments,name',
            'parent_id' => 'nullable|integer',
        ]);

        $department = Department::Create([
            'organization_id' => $organizationId,
            'name' => $request->name,
            'parent_id' => (!empty($request->parent_id)) ? $request->parent_id : 0,
        ]);

        Log::info('User has created a new department', [
            'department_id' => $department->id
        ]);
        return redirect()->back()->withSuccess('Department added successfully!');
    }

    public function edit(Request $request, $organizationId, $departmentId)
    {
        $department = Department::where('organization_id', $organizationId)
            ->where('id', $departmentId)
            ->first();
        Log::info('User is attempting to edit a department', [
            'department_id' => $department->id
        ]);
        return response()->json([
            'success' => true,
            'data' => $department,
        ]);
    }

    /**
     * Updates a department.
     */
    public function update(Request $request, $organizationId, $departmentId)
    {
        $request->validate([
            'name' => 'required|unique:departments,name,' . $departmentId,
            'parent_id' => 'nullable',
        ]);

        $department = Department::where('organization_id', $organizationId)
            ->where('id', $departmentId)
            ->first();

        if (!$department) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Modal not found',
            ]);
        }

        $department->name = $request->name;

        if ($request->parent_id) {
            $department->parent_id = $request->parent_id;
        }

        $department->update();

        Log::info('User has updated a department', [
            'department_id' => $department->id
        ]);
        return redirect()->back()->withSuccess('Department updated successfully!');
    }

    public function delete(Request $request, $organizationId, $departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $departmentTobeDeleted = $department->getAllChildDepartIds();
        Log::info('User is attempting to delete department', [
            'department_id' => $department->id
        ]);

        /* push parent depart id to child depart array */
        $departmentTobeDeleted[] = $department->id;

        /* Getting all user associated with departments*/
        $departmentUsers = AdminDepartment::whereIn('department_id', $departmentTobeDeleted)->count();


        if ($departmentUsers > 0) {
            Log::info('Department could not be deleted', [
                'department_id' => $department->id
            ]);
            return response()->json(['success' => false]);
        } else {
            Department::whereIn('id', $departmentTobeDeleted)->delete();
            Log::info('User has deleted department', [
                'department_id' => $department->id
            ]);
            return response()->json(['success' => true]);
        }
    }

    public function getTransferableDepartments(Request $request, $organizationId, $departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $allDepart = [];
        $allDepart[] = $department->id;
        $allChildDepart = $department->getAllChildDepartIds();
        $allDepart = array_merge($allChildDepart, $allDepart);
        $transferableDeparts = Department::whereNotIn('id', $allDepart)->get();

        return response()->json(['success' => true, 'data' => $transferableDeparts]);
    }

    public function userDepartmentTransfer(Request $request, $organizationId, $departmentId)
    {
        if (!$request->transfer_to) {
            return response()->json(['success' => false]);
        }

        $department = Department::findOrFail($departmentId);
        $allChildDepart = $department->getAllChildDepartIds();

        /* push parent depart id to child depart array */
        $allChildDepart[] = $department->id;

        AdminDepartment::whereIn('department_id', $allChildDepart)->update([
            'department_id' => $request->transfer_to
        ]);

        return redirect()->back()->with(['success' => 'Users transferred successfully.']);
    }

    public function getDepartmentCount(Request $request, $organizationId, $departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $allDepart = [];
        $allDepart[] = $department->id;
        $allChildDepart = $department->getAllChildDepartIds();
        $allDepart = array_merge($allChildDepart, $allDepart);


        $allUsersCount = AdminDepartment::whereIn('department_id', $allDepart)->count();

        return response()->json(['success' => true, 'data' => $allUsersCount]);
    }

    /*
    * Child department tree view data
    */
    public function departmentFilterTreeViewData(Request $request)
    {
        $request->validate([
            'data_scope' => 'required'
        ]);

        /* Handling data scoping data*/
        $dataScopeData = explode('-', $request->data_scope);
        $dataScopeType =  $dataScopeData[1] > 0 ? 'department' : 'organization';
        $parentId = ($dataScopeType == 'department') ? $dataScopeData[1] : 0;
        $departments = Department::where('parent_id', $parentId)->get();

        $treeViewData = $this->departmentFilterTreeViewBuilder($departments);

        return response()->json([
            'success' => true,
            'data' => $treeViewData
        ]);
    }

    public function getAllDepartmentFilterTreeViewData()
    {
        $departments = Department::where('parent_id', 0)->get();

        $treeViewData = $this->departmentFilterTreeViewBuilder($departments);

        return response()->json([
            'success' => true,
            'data' => $treeViewData
        ]);
    }

    /*
    * creating the tree structure tree view data
    */
    private function departmentFilterTreeViewBuilder($departments, $data = [])
    {
        foreach ($departments as $department) {
            $topNode = [
                'key' => $department->id,
                'title' => $department->name
            ];

            if ($department->departments()->count() > 0) {
                $childNode = self::departmentFilterTreeViewBuilder($department->departments);
                $topNode['children'] = $childNode;
            }

            $data[] = $topNode;
        }

        return $data;
    }
}
