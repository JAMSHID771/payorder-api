<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|regex:/^998[0-9]{9}$/|unique:users,phone',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Yangi telefon raqami kiritish majburiy',
            'phone.regex' => 'Telefon raqami notogri formatda',
            'phone.unique' => 'Bu telefon raqami allaqachon royxatdan otgan',
        ];
    }
}
