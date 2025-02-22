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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'payment_method' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'exists:gold_items,id',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0'
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
