<?php

namespace App\Rules\PolicyManagement;

use App\Models\PolicyManagement\Policy;
use Illuminate\Contracts\Validation\Rule;

class LinkPolicyUniqueName implements Rule
{

    private $requestPayload;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->requestPayload = request()->all();
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
        $currentIndex = explode('.', $attribute)[1];
        $currentVersion = $this->requestPayload['version'][$currentIndex];
        $isValid = $this->validateRequestPayload($currentIndex, $value, $currentVersion);



        return !$isValid ? $isValid :  $this->validateDb($value, $currentVersion);
    }

    private function validateRequestPayload($currentIndex, $currentValue, $currentVersion)
    {
        $isValid = true;

        foreach ($this->requestPayload['display_name'] as $key => $displayName) {
            if ($currentIndex == $key) {
                continue;
            }

            if ($displayName == $currentValue && ($this->requestPayload['version'][$key] == $currentVersion)) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }

    private function validateDb($currentValue, $currentVersion)
    {
        $dataScope = explode('-', request('data_scope'));
        $organizationId = $dataScope[0];
        $departmentId = $dataScope[1];

        return !Policy::query()->whereHas('scope', function ($query) use ($organizationId, $departmentId) {
            $query->where('organization_id', $organizationId);
            if ($departmentId == 0) {
                $query->whereNull('department_id');
            } else {
                $query->where('department_id', $departmentId);
            }
        })->where('display_name', clean($currentValue))->where('version', clean($currentVersion))->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Duplicate policy name(s) detected.';
    }
}
