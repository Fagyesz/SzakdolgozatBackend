<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Models\User;
use App\Rules\RequiredExcept;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[Pure]
    #[ArrayShape(['begin_time' => 'array', 'end_time' => 'array', 'note' => 'string[]', 'user_id' => 'array', 'service_id' => 'array', 'token' => 'array'])]
    public function rules(): array
    {
        return [
            'begin_time' => [(new RequiredExcept('patch'))->without('token'), 'date', 'date_format:Y-m-d H:i:s', 'after_or_equal:now'],
            'end_time' => [(new RequiredExcept('patch'))->without('token'), 'date', 'date_format:Y-m-d H:i:s', 'after_or_equal:begin_time'],
            'note' => ['string'],
            'user_id' => ['string', Rule::exists((new User)->getTable(), 'id')],
            'service_id' => ['required_without:token', 'string', Rule::exists((new Service)->getTable(), 'id')],
            'token' => ['string', Rule::exists('authed_appointments', 'id'), 'required_without:begin_time,end_time,user_id,service_id']
        ];
    }
}
