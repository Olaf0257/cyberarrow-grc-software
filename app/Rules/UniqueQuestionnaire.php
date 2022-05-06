<?php

namespace App\Rules;

use App\Models\ThirdPartyRisk\Questionnaire;
use Illuminate\Contracts\Validation\Rule;

class UniqueQuestionnaire implements Rule
{
    private $version, $ignore;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($version, $ignore = null)
    {
        $this->version = $version;
        $this->ignore = $ignore;
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
        return !Questionnaire::query()
            ->where('name', clean($value))
            ->where('version', clean($this->version))
            ->where('id', '<>', $this->ignore)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This questionnaire already exists.';
    }
}
