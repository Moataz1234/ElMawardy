<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoldItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serial_number' => 'required|string',
            'shop_name' => 'required|string',
            'shop_id' => 'required|integer',
            'kind' => 'required|string',
            'model' => 'required|string',
            'talab' => 'required|string',
            'gold_color' => 'required|string',
            'stones' => 'string|nullable',
            'metal_type' => 'required|string',
            'metal_purity' => 'required|string',
            'quantity' => 'required|integer',
            'weight' => 'required|numeric',
            'rest_since' => 'date|nullable',
            ];
    }
}