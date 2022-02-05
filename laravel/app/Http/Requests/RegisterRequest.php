<?php

namespace App\Http\Requests;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['name' => "string[]", 'email' => "array", 'password' => "array"])]
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'string', 'email', Rule::unique('users', 'email')],
            'password' => ['required', new Password]
        ];
    }
}
