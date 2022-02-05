<?php

namespace App\Http\Requests;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['email' => "array", 'password' => "array", 'remember_me' => "string[]"])]
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', Rule::exists('users', 'email')],
            'password' => ['required', 'string'],
            'remember_me' => ['boolean']
        ];
    }

    public function credentials(): array
    {
        return $this->except('remember_me');
    }
}
