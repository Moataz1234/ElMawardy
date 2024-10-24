<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoldItemRequest extends FormRequest
{

   public function rules()
    {
        return [
            'shop_id' => 'required|integer',
            'kind' => 'required|string',
            'model' => 'required|string',
            'gold_color' => 'required|string',
            'metal_type' => 'required|string',
            'metal_purity' => 'required|string',
            'quantity' => 'required|integer',
            'weight' => 'required|numeric',
            'source' => 'required|string',
            'link' => 'nullable|file|image'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
