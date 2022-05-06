<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateUrlOrNetworkFolder implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) || preg_match('/^(\\\\)(\\\\[\\w \\.-_]+){2,}(\\\\?)$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not a valid url or a shared network folder.';
    }
}
