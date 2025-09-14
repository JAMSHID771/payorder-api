<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^998[0-9]{9}$/|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism kiritish majburiy',
            'last_name.required' => 'Familiya kiritish majburiy',
            'phone.required' => 'Telefon raqami kiritish majburiy',
            'phone.regex' => 'Telefon raqami notogri formatda',
            'phone.unique' => 'Bu telefon raqami allaqachon royxatdan otgan',
            'email.required' => 'Email kiritish majburiy',
            'email.email' => 'Email notogri formatda',
            'email.unique' => 'Bu email allaqachon royxatdan otgan',
            'password.required' => 'Parol kiritish majburiy',
            'password.min' => 'Parol kamida 6 ta belgi bolishi kerak',
            'password.confirmed' => 'Parol tasdiqlanmadi',
            'avatar.image' => 'Avatar rasm formatida bolishi kerak',
            'avatar.max' => 'Avatar hajmi 2MB dan oshmasligi kerak',
        ];
    }
}


