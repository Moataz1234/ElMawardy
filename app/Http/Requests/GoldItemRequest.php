<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class GoldItemRequest extends FormRequest
{
    // In GoldItemRequest.php
    public function rules()
    {
        return [
            'kind' => 'required|string',
            'model' => 'required|string',  // Change from model_number to model
            'metal_type' => 'required|string',
            'metal_purity' => 'required|string',
            'quantity' => 'required|integer',
            'shops' => 'required|array|min:1',
            'shops.*.talab' => 'nullable|boolean',
            'shops.*.shop_id' => 'required|integer|exists:shops,id',
            'shops.*.gold_color' => 'required|string',
            'shops.*.weight' => 'required|numeric|min:0.01',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            Log::info('Running additional validation', [
                'is_talabat' => $this->has('is_talabat'),
                'model' => $this->input('model'),  // Change from model_number to model
            ]);

            if ($this->has('is_talabat')) {
                $exists = \App\Models\Talabat::where('model', $this->input('model'))->exists();
                Log::info('Checking talabat model exists', [
                    'model_input' => $this->input('model'),
                    'exists' => $exists
                ]);

                if (!$exists) {
                    $validator->errors()->add('model', 'The selected talabat model does not exist.');
                }
            } else {
                $exists = \App\Models\Models::where('model', $this->input('model'))->exists();
                Log::info('Checking regular model exists', [
                    'model_input' => $this->input('model'),
                    'exists' => $exists
                ]);

                if (!$exists) {
                    $validator->errors()->add('model', 'The selected model does not exist.');
                }
            }
        });
    }
    public function authorize()
    {
        return true;
    }
}
