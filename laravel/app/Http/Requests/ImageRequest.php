<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;
use Str;

/**
 * @property string $entity
 */
class ImageRequest extends ExtendedFormRequest
{
    #[ArrayShape(['file' => 'string[]'])]
    public function rules(): array
    {
        return [
            'file' => [] //['image', 'mimes:jpeg,jpg,png,gif,tiff,bmp,webp', 'max:10240'], //max 10MB
        ];
    }
}
