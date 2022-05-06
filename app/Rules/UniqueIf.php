<?php

namespace App\Rules;

use App\Models\PolicyManagement\Policy;
use Illuminate\Contracts\Validation\Rule;

class UniqueIf implements Rule
{
    private $version;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($version)
    {
        $this->version = $version;
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
        return !Policy::query()->where('display_name', clean($value))->where('version', clean($this->version))->exists();
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
