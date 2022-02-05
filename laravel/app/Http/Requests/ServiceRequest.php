<?php

namespace App\Http\Requests;

use App\Rules\RequiredExcept;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property string image_id
 */
class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['name' => "array", 'description' => "array", 'image_id' => "array", 'user_id' => "array"])]
    public function rules(): array
    {
        return [
            'name' => [new RequiredExcept('patch') ,'string', 'min:3'],
            'description' => [new RequiredExcept('patch'), 'string', 'min:3'],
            'image_id' => ['string', Rule::exists('images', 'id')],
            'user_id' => [new RequiredExcept('patch'), 'string', Rule::exists('users', 'id')],
        ];
    }
}
