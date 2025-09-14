<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'sometimes|nullable|string|max:255',
			'price' => 'sometimes|nullable|numeric|min:0',
		];
	}
}


