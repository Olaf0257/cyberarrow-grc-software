<?php

namespace App\Rules\PolicyManagement;

use Illuminate\Contracts\Validation\Rule;
use App\Models\PolicyManagement\Policy;

class UniquePolicyName implements Rule
{
    private $version;
    private $policyId = null;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($version, $id = null)
    {
        $this->version = $version;

        if ($id) {
            $this->policyId = $id;
        }
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
        $dataScope = explode('-', request('data_scope'));
        $organizationId = $dataScope[0];
        $departmentId = $dataScope[1];

        $baseQuery = Policy::query()->whereHas('scope', function ($query) use ($organizationId, $departmentId) {
            $query->where('organization_id', $organizationId);
            if ($departmentId == 0) {
                $query->whereNull('department_id');
            } else {
                $query->where('department_id', $departmentId);
            }
        })->where('display_name', clean($value))->where('version', clean($this->version));


        if ($this->policyId) {
            $isValid = !$baseQuery->where('id', '!=', $this->policyId)->exists();
        } else {
            $isValid = !$baseQuery->exists();
        }
        return $isValid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This policy already exists.';
    }
}
