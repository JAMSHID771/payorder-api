<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'price' => 'sometimes|nullable|numeric|min:0',
			'product_id' => 'sometimes|nullable|exists:products,id',
			'status' => 'sometimes|nullable|string|in:kutilmoqda,jarayonda,yakunlandi,bekor_qilingan',
		];
	}
}


