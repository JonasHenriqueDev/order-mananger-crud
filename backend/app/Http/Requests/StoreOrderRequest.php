<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'tax' => ['sometimes', 'numeric', 'min:0'],
            'discount' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['sometimes', 'string', 'nullable'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $user = auth()->user();
                $requestedUserId = $this->input('user_id');

                if ($requestedUserId && !$user->hasAnyRole(['admin', 'manager'])) {
                    if ($requestedUserId != $user->id) {
                        $validator->errors()->add(
                            'user_id',
                            'You can only create orders in your own name.'
                        );
                    }
                }

                $items = $this->input('items', []);

                foreach ($items as $index => $item) {
                    $product = Product::find($item['product_id'] ?? null);

                    if (!$product) {
                        continue;
                    }

                    if ($product->stock < ($item['quantity'] ?? 0)) {
                        $validator->errors()->add(
                            "items.{$index}.quantity",
                            "Insufficient stock for product '{$product->name}'. Available: {$product->stock}"
                        );
                    }

                    if ($product->status !== 'active') {
                        $validator->errors()->add(
                            "items.{$index}.product_id",
                            "Product '{$product->name}' is not active."
                        );
                    }
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'The order must contain at least one item.',
            'items.*.product_id.required' => 'Product ID is required.',
            'items.*.product_id.exists' => 'Product not found.',
            'items.*.quantity.required' => 'Quantity is required.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
