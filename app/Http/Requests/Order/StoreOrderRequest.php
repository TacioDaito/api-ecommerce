<?php
namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;

class StoreOrderRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
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
            'products.*.id.required' => 'Each product must have an ID.',
            'products.*.id.exists' => 'The selected product does not exist.',
            'products.*.quantity.required' => 'Each product must have a quantity.',
            'products.*.quantity.integer' => 'Product quantity must be an integer.',
            'products.*.quantity.min' => 'Product quantity must be at least 1.',
        ];
    }
}
