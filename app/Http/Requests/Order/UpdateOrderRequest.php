<?php

namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;
// use Gate;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('accessOrder', $this->route('order'));
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'products.required' => 'At least one product is required.',
            'products.array' => 'Products must be an array.',
            'products.*.id.required_with' => 'Each product must have an ID when products are provided.',
            'products.*.id.exists' => 'The selected product does not exist.',
            'products.*.quantity.required_with' => 'Each product must have a quantity when products are provided.',
            'products.*.quantity.integer' => 'Product quantity must be an integer.',
            'products.*.quantity.min' => 'Product quantity must be at least 1.',
        ];
    }
}
