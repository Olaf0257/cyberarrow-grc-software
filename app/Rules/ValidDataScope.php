<?php

namespace App\Rules;

use App\Models\Administration\OrganizationManagement\Department;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Administration\OrganizationManagement\Organization;

class ValidDataScope implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $dataScopeData = explode('-', $value);

        /* not a valid data format if data scope data array is not equal to Two*/
        if (count($dataScopeData) !== 2) {
            return false;
        }

        /* checking data scope organization is valid */
        $organization = Organization::where('id', $dataScopeData[0])->first();
        if (!$organization) {
            return false;
        }

        if ($dataScopeData[1] != '0') {
            $department =  Department::where('id', $dataScopeData[1])->first();

            if (!$department) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The provided data Scope is invalid.';
    }
}
