<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use JetBrains\PhpStorm\Pure;

class RequiredExcept implements Rule
{
    private array $except;

    /**
     * Create a new rule instance.
     *
     * @param string[] $except
     * @return void
     */
    public function __construct(...$except)
    {
        $this->except = $except;
    }

    public function passes($attribute, $value): bool
    {
        return request()->has($attribute) || array_search(request()->getMethod(), $this->except);
    }

    public function message(): string
    {
        return 'The :attribute field is required.';
    }

    #[Pure]
    public function without(string $fields): RequiredExceptWithout
    {
        return new RequiredExceptWithout($fields);
    }
}
