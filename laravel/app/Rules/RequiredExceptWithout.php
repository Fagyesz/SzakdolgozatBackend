<?php

namespace App\Rules;

use JetBrains\PhpStorm\Pure;

class RequiredExceptWithout extends RequiredExcept
{
    #[Pure] public function __construct(private string $except)
    {
        parent::__construct();
    }

    public function passes($attribute, $value): bool
    {
        return request()->has($this->except) || parent::passes($attribute, $value);
    }
}
