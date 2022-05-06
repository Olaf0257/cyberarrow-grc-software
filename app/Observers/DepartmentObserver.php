<?php

namespace App\Observers;

use App\Models\Administration\OrganizationManagement\Department;
use App\Models\DataScope\Scopable;
use App\Models\ThirdPartyRisk\Questionnaire;
use Illuminate\Support\Facades\Log;

class DepartmentObserver
{
    /**
     * Handle the Department "created" event.
     *
     * @param  App\Models\Administration\OrganizationManagement\Department  $department
     * @return void
     */
    public function created(Department $department)
    {
        $default_questionnaire = Questionnaire::where('name', 'Default')->where('is_default', 1)->first();
        if ($default_questionnaire) {
            Scopable::create([
                'organization_id' => $department->organization_id,
                'department_id' => $department->id,
                'scopable_id' => $default_questionnaire->id,
                'scopable_type' => get_class($default_questionnaire),
            ]);
            Log::info('Default questionnaire was added to newly created department.', ['department_id' => $department->id]);
        }
    }
}
