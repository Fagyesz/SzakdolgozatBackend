<?php

namespace App\Http\Requests;

class TenantRequest extends ExtendedFormRequest
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'description' => ['required', 'string', 'min:3']
        ];
    }
}
