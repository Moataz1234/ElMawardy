<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'payment_method' => 'required|string|max:50',
            'ids' => 'required|array',
            'ids.*' => 'exists:gold_items,id',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
            'pound_prices' => 'sometimes|array',
            'pound_prices.*' => 'sometimes|numeric|min:0',
            'sold_date' => 'nullable|date'
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Please select at least one item to sell',
            'prices.*.required' => 'Price is required for all selected items',
            'prices.*.numeric' => 'Price must be a number',
            'prices.*.min' => 'Price cannot be negative'
        ];
    }
}
