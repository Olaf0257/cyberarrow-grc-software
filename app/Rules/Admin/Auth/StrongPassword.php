<?php

namespace App\Rules\Admin\Auth;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    /**
     * Create a new rule instance.
     */
    public function __construct()
    {
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
        //Minimum eight characters upper lower case, special characters and numbers
        $regex = '^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[@$!%*?&])(?=\S*[\d])\S*$';

        if (preg_match('/'.$regex.'/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '
                Password must contain:
                <ul style="padding-left: 1.5rem;">
                    <li> a minimum of 8 characters and </li>
                    <li> a minimum of 1 lower case letter and </li>
                    <li> a minimum of 1 upper case letter and </li>
                    <li> a minimum of 1 special character and </li>
                    <li> a minimum of 1 numeric character </li>
                </ul>
        ';
    }
}
