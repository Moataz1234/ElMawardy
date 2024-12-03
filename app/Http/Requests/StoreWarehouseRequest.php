<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serial_number' => 'required|string',
            'kind' => 'required|string',
            'model' => 'required|string',
            'talab' => 'required|string',
            'gold_color' => 'required|string',
            'stones' => 'required|string',
            'metal_type' => 'required|string',
            'metal_purity' => 'required|string',
            'quantity' => 'required|integer',
            'weight' => 'required|numeric',
            'rest_since' => 'required|date',
            'source' => 'required|string',
            'to_print' => 'boolean',
            'price' => 'required|numeric',
            'semi_or_no' => 'required|string',
            'average_of_stones' => 'required|numeric',
            'net_weight' => 'required|numeric'
        ];
    }
}