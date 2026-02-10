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
                $items = $this->input('items', []);

                foreach ($items as $index => $item) {
                    $product = Product::find($item['product_id'] ?? null);

                    if (!$product) {
                        continue;
                    }

                    // Validate stock availability
                    if ($product->stock < ($item['quantity'] ?? 0)) {
                        $validator->errors()->add(
                            "items.{$index}.quantity",
                            "Estoque insuficiente para o produto '{$product->name}'. Disponível: {$product->stock}"
                        );
                    }

                    // Validate product is active
                    if ($product->status !== 'active') {
                        $validator->errors()->add(
                            "items.{$index}.product_id",
                            "O produto '{$product->name}' não está ativo."
                        );
                    }
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'O pedido deve conter pelo menos um item.',
            'items.*.product_id.required' => 'O ID do produto é obrigatório.',
            'items.*.product_id.exists' => 'Produto não encontrado.',
            'items.*.quantity.required' => 'A quantidade é obrigatória.',
            'items.*.quantity.min' => 'A quantidade deve ser no mínimo 1.',
        ];
    }
}
