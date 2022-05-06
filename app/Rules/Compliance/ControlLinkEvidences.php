<?php

namespace App\Rules\Compliance;

use Illuminate\Contracts\Validation\Rule;

class ControlLinkEvidences implements Rule
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
        
        $isValidURL = filter_var($value, FILTER_VALIDATE_URL);


        $re = '/^(\\\\)(\\\\[\w\.\s\-_]+){2,}(\\\\?)$/';
        preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);


        $isNetworkShareFolderLink = count($matches) > 0 ? true : false;

        return $isValidURL || $isNetworkShareFolderLink;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
