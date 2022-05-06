<?php

namespace App\Rules\RiskManagement;

use Illuminate\Contracts\Validation\Rule;

class ValidRiskAffectedProperties implements Rule
{
    public $validAffectedProperties = [
        'Confidentiality',
        'Integrity',
        'Availability',
        'Change Management', 'Ethical', 'Financial', 'Financial Reporting', 'Fraud', 'Geographic',
        'IT Operations', 'Logical Access', 'Material Misstatement', 'Operational', 'Privacy', 'Regulatory / Compliance',
         'Reputational', 'Strategy',
    ];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->notFoundAttributes=[];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (gettype($value) == 'string') {
            $value = explode(',', $value);
        }

        $notFoundAttributes= array_diff($value,$this->validAffectedProperties);

        if (count($notFoundAttributes) > 0) {
            $this->notFoundAttributes=implode(',',$notFoundAttributes);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid :attribute '.$this->notFoundAttributes.' .';
    }
}
