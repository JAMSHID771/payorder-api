<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResendSmsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'required|string|regex:/^998[0-9]{9}$/',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => 'Telefon raqami kiritilishi shart',
            'phone.regex' => 'Telefon raqami notogri formatda',
        ];
    }
}
