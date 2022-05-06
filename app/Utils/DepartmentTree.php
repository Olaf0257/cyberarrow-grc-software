<?php

namespace App\Utils;

use App\Models\Administration\OrganizationManagement\Department;
use App\Models\Administration\OrganizationManagement\Organization;

class DepartmentTree {
    public function getTreeData()
    {
        $data = [];
        $organization = Organization::first();

        if (!$organization) {
           return $data;
        }

        $allDepartments = Department::where('parent_id', 0)->with('departments')->get();

        $data[] = [
            'label' => $organization->name,
            'value' => 0,
        ];

        foreach($allDepartments  as $department){

            $node = [
                'label' => $department->name,
                'value' => $department->id,
            ];


            if($department->departments()->exists()){
                $node = $this->buildTreeDepartments($department->departments, $node);
            }


            $data[0]['children'][] = $node;

        }

        return $data;
    }

 /*
    * Creates department data in tree views
    */
    public function buildTreeDepartments($departments, $node){
        $node['children'] = [];
        foreach ($departments as $key => $department) {
            $childData = [
                'label' => $department->name,
                'value' => $department->id,
            ];

            if($department->departments()->exists()){
                $childData = self::buildTreeDepartments($department->departments, $childData);
            }

            $node['children'][] = $childData;
        }

        return $node;
    }



}
