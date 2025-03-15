<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models;
use Illuminate\Support\Facades\Validator;

class ModelsController extends Controller
{
    public function index()
    {
        $models = Models::all();
        return response()->json($models);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model' => 'required|unique:models',
            'SKU' => 'required|unique:models',
            'scanned_image' => 'nullable|string',
            'website_image' => 'nullable|string',
            'stars' => 'nullable|string',
            'source' => 'nullable|string',
            'first_production' => 'nullable|string',
            'semi_or_no' => 'nullable|string',
            'average_of_stones' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $model = Models::create($request->all());
        return response()->json($model, 201);
    }

    public function show($id)
    {
        $model = Models::find($id);
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }
        return response()->json($model);
    }

    public function update(Request $request, $id)
    {
        $model = Models::find($id);
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'model' => 'unique:models,model,' . $id,
            'SKU' => 'unique:models,SKU,' . $id,
            'scanned_image' => 'nullable|string',
            'website_image' => 'nullable|string',
            'stars' => 'nullable|string',
            'source' => 'nullable|string',
            'first_production' => 'nullable|string',
            'semi_or_no' => 'nullable|string',
            'average_of_stones' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $model->update($request->all());
        return response()->json($model);
    }

    public function destroy($id)
    {
        $model = Models::find($id);
        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }
        
        $model->delete();
        return response()->json(['message' => 'Model deleted successfully']);
    }
} 