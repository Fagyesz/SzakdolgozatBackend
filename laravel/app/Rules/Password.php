<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Password implements Rule
{
    public static function toArray(): array
    {
        return [
            'confirmed',          // this means there is a password_confirmation input, which must match this
            'string',             // regex is buggy without specifying the type, probably a bug with the validation engine
            'min:10',             // must be at least 10 characters in length
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
            'regex:/[@$!%*#?&-.,:_;<>()\[\]\"]/', // must contain a special character
        ];
    }

    public function passes($attribute, $value): bool
    {
        return ( preg_match('/[a-z]/', $value)
            && preg_match('/[A-Z]/', $value)
            && preg_match('/[0-9]/', $value)
            && preg_match('/[@$!%*#?&-.,:_;<>()\[\]\"]/', $value));
    }

    public function message(): string
    {
        return 'Password is not strong enough';
    }
}
