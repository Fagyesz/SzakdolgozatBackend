<?php

namespace App\Http\Requests;

use App\Rules\RequiredExcept;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property string image_id
 */
class PostRequest extends ExtendedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(['title' => 'string[]', 'content' => 'string[]', 'published_at' => 'string[]', 'image_id' => 'array'])]
    public function rules(): array
    {
        return [
            'title' => [ new RequiredExcept('patch'), 'string', 'min:3'],
            'content' => [new RequiredExcept('patch'), 'string', 'min:3'],
            'published_at' => ['date', 'date_format:Y-m-d H:i:s'],
            'image_id' => ['string', Rule::exists('images', 'id')]
        ];
    }
}
