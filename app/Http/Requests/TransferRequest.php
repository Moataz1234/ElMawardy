<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
        'shop_name' => [
            'required',
            'exists:users,shop_name,usertype,user',
            function ($attribute, $value, $fail) {
                if ($value === Auth::user()->shop_name) {
                    $fail('Cannot transfer to the same shop.');
                }
            },
        ],
        'item_ids' => 'required|array',
        'item_ids.*' => 'exists:gold_items,id'
    ];
}
}
