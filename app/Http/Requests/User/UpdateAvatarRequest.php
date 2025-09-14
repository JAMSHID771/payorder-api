<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Avatar fayli kiritish majburiy',
            'avatar.image' => 'Avatar rasm formatida bolishi kerak',
            'avatar.mimes' => 'Avatar jpeg, png, jpg yoki gif formatida bolishi kerak',
            'avatar.max' => 'Avatar hajmi 2MB dan oshmasligi kerak',
        ];
    }
}
