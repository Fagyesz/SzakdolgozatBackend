<?php

namespace App\Actions\Fortify;

use App\Rules\Password;
use JetBrains\PhpStorm\Pure;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    #[Pure] protected function passwordRules(): array
    {
        return ['required', 'string', new Password, 'confirmed'];
    }
}
