<?php

namespace App\Rules\Compliance;

use App\Models\Compliance\StandardControl;
use Illuminate\Contracts\Validation\Rule;

class StandardControlUniqueId implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $primary_id;
    private $standard_id;
    /**
     * @var mixed|null
     */
    private $control_id;

    public function __construct($primary_id, $standard_id, $control_id = null)
    {
        $this->primary_id = $primary_id;
        $this->standard_id = $standard_id;
        $this->control_id = $control_id;
    }

    /**
     * Determine if the validation rule passes.
     * Verify that the pair of ids is unique
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $id_exists = StandardControl::where('standard_id', $this->standard_id)
            ->where('primary_id', $this->primary_id)
            ->where('sub_id', $value)
            ->where('id', '!=', $this->control_id)
            ->exists();
        if ($id_exists) {
            return false;
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
        return 'A control with this Sub ID already exists for this Primary ID.';
    }
}
