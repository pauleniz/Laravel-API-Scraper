<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidSelector implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // A basic regex pattern to validate CSS selectors
        // This can be extended for more complex cases
        $pattern = '/^[a-zA-Z0-9\.\#\-\_\[\]\=\"\:\s\>\+\~\*]+$/';

        return preg_match($pattern, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not a valid CSS selector.';
    }
}
