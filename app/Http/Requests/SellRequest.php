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
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'payment_method' => 'required|string',
            'serial_numbers' => 'required|array',
            'serial_numbers.*' => 'exists:gold_pounds_inventory,serial_number',
            'prices' => 'required|array',
            'prices.*' => 'numeric|min:0'
        ];
    }
}
