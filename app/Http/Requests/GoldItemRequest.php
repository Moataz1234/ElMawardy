<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoldItemRequest extends FormRequest
{

   public function rules()
    {
        return [
            'kind' => 'required|string',
            'model' => 'required|string',
            'metal_type' => 'required|string',
            'metal_purity' => 'required|string',
            'quantity' => 'required|integer',
            'talab' => 'nullable|boolean',
            'shops' => 'required|array|min:1', // At least one shop is required
            'shops.*.shop_id' => 'required|integer|exists:shops,id',
            'shops.*.gold_color' => 'required|string',
            'shops.*.weight' => 'required|numeric|min:0.01',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
