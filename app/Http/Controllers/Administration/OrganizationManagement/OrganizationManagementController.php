<?php

namespace App\Http\Controllers\Administration\OrganizationManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Administration\OrganizationManagement\Organization;

class OrganizationManagementController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:departments,name'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with([
                'error' => 'Organization creation failed. Name field can not be empty.',
                'activeTab' => 'organizations',
            ]);
        }

        $organization = new Organization();
        $organization->name = $request->name;
        $organization->save();

        //for add campaign response
        return redirect()->back()->with([
            'success' => 'Organization created successfully.',
            'activeTab' => 'organizations',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:departments,name'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with([
                'error' => 'Organization update failed. Name field can not be empty.',
                'activeTab' => 'organizations',

            ]);
        }

        $organization = Organization:: findOrFail($id);
        $organization->name = $request->name;
        $organization->update();

        //for add campaign response
        return redirect()->back()->with([
            'success' => 'Organization updated successfully.',
            'activeTab' => 'organizations',
        ]);
    }

    public function edit($id)
    {
        $organization = Organization:: findOrFail($id);
        //for add campaign response
        return redirect()->back()->with([
            'success' => 'Organization updated successfully.',
            'activeTab' => 'organizations',
        ]);
    }

    public function delete($id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();

        return redirect()->back()->with([
            'success' => 'Organization deleted successfully.',
            'activeTab' => 'organizations',
        ]);
    }
}
