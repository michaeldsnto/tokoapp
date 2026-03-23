<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', Rule::unique('products', 'code')->ignore($productId)],
            'category_id' => ['required', 'exists:categories,id'],
            'price_per_unit' => ['required', 'numeric', 'min:0'],
            'price_per_pack' => ['required', 'numeric', 'min:0', 'gt:price_per_dozen'],
            'price_per_dozen' => ['required', 'numeric', 'min:0', 'gt:price_per_unit'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'price_per_pack.gt' => 'Harga pak harus lebih mahal daripada harga lusin.',
            'price_per_dozen.gt' => 'Harga lusin harus lebih mahal daripada harga satuan.',
        ];
    }
}
