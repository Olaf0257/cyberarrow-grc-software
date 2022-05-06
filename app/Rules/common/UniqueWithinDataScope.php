<?php

namespace App\Rules\common;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueWithinDataScope implements Rule
{
    protected $model;
    protected $field;
    protected $updateRowId = null;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($model, $field, $updateRowId = null)
    {
        $this->model = $model;
        $this->field = $field;

        if (!is_null($updateRowId)) {
            $this->updateRowId = $updateRowId;
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
        return $this->validate($value);
    }


    private function validate($value)
    {
        $baseQuery = $this->model->where($this->field, $value);

        /* Handling the update case */
        if (!is_null($this->updateRowId)) {
            $baseQuery->where('id', '!=', $this->updateRowId);
        }


        return !$baseQuery->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
