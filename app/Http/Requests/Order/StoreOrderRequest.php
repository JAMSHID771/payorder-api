<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'price' => 'required|numeric|min:0',
			'product_id' => 'required|exists:products,id',
			'status' => 'nullable|string|in:kutilmoqda,jarayonda,yakunlandi,bekor_qilingan',
		];
	}
}


