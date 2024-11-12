<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'order_kind' => 'required|string|max:255',
            'order_fix_type' => 'required|string|max:255',
            'ring_size' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'gold_color' => 'nullable|string|max:255',
            'order_details' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'seller_name' => 'nullable|string|max:255',
            'deposit' => 'nullable|numeric',
            'rest_of_cost' => 'nullable|numeric',
            'order_date' => 'nullable|date',
            'deliver_date' => 'nullable|date',
            'status' => 'required|string',
            'image_link' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}