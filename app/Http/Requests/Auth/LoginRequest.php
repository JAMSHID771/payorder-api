<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email kiritish majburiy',
            'email.email' => 'Email notogri formatda',
            'password.required' => 'Parol kiritish majburiy',
            'password.min' => 'Parol kamida 6 ta belgi bolishi kerak',
        ];
    }
}


