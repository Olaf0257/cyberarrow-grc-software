<?php

namespace App\Rules\Compliance;

use Illuminate\Contracts\Validation\Rule;

class AllowedEvidence implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $approved_file_types;

    public function __construct()
    {
        $this->approved_file_types = ['doc','docx','ppt','pptx','xls','xlsx','jpg','png','jpeg','gif','pdf','msg','eml'];
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
        $clientMime = $value->getClientMimeType();
        if (($clientMime === 'message/rfc822') || in_array($value->guessExtension(), $this->approved_file_types)) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The evidences must be a file of type: ' . implode(',', $this->approved_file_types);
    }
}
