<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\Password;
use App\Rules\RequiredExcept;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed image_id
 */
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['name' => "array", 'email' => "array", 'password' => "\App\Rules\Password", 'troll' => "string[]"])]
    public function rules(): array
    {
        $rules = [
            'name' => [new RequiredExcept('patch'), 'string', 'min:3'],
            'email' => [new RequiredExcept('patch'), 'string', 'email', Rule::unique('users', 'email')->ignore(request()->route('user'))],
            'image_id' => ['string', Rule::exists('images', 'id')],
        ];
        if (Auth::user()->can('mark-as-troll', User::class))
            $rules['troll'] = ['boolean'];

        if (Auth::id() === request()->route('user') && request()->has('password'))
            $rules['password'] = new Password();

        return $rules;
    }
}
