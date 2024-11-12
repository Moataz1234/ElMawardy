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
            'ids' => 'required|array',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required',
            'address' => 'required|string|max:255',
            'email' => 'required|email',
            'payment_method' => 'required'
        ];
    }
}
