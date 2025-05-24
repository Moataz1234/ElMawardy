<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models;
use Illuminate\Support\Facades\Storage;

class SuperModelsController extends Controller
{
    public function index()
    {
        $models = Models::orderBy('created_at', 'desc')->paginate(20);
        return view('super.models.index', compact('models'));
    }

    public function create()
    {
        return view('super.models.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required|string|max:255|unique:models,model',
            'SKU' => 'nullable|string|max:255',
            'scanned_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stars' => 'nullable|integer|min:1|max:5',
            'source' => 'nullable|string|max:255',
            'first_production' => 'nullable|date',
            'semi_or_no' => 'nullable|string|max:255',
            'average_of_stones' => 'nullable|numeric',
        ]);

        $data = $request->all();

        // Handle image uploads
        if ($request->hasFile('scanned_image')) {
            $data['scanned_image'] = $request->file('scanned_image')->store('models/scanned', 'public');
        }

        if ($request->hasFile('website_image')) {
            $data['website_image'] = $request->file('website_image')->store('models/website', 'public');
        }

        Models::create($data);

        return redirect()->route('super.models.index')->with('success', 'Model created successfully');
    }

    public function show($id)
    {
        $model = Models::with(['goldItems', 'goldItemsAvg'])->findOrFail($id);
        return view('super.models.show', compact('model'));
    }

    public function edit($id)
    {
        $model = Models::findOrFail($id);
        return view('super.models.edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = Models::findOrFail($id);
        
        $request->validate([
            'model' => 'required|string|max:255|unique:models,model,' . $id,
            'SKU' => 'nullable|string|max:255',
            'scanned_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stars' => 'nullable|integer|min:1|max:5',
            'source' => 'nullable|string|max:255',
            'first_production' => 'nullable|date',
            'semi_or_no' => 'nullable|string|max:255',
            'average_of_stones' => 'nullable|numeric',
        ]);

        $data = $request->all();

        // Handle image uploads
        if ($request->hasFile('scanned_image')) {
            // Delete old image
            if ($model->scanned_image) {
                Storage::disk('public')->delete($model->scanned_image);
            }
            $data['scanned_image'] = $request->file('scanned_image')->store('models/scanned', 'public');
        }

        if ($request->hasFile('website_image')) {
            // Delete old image
            if ($model->website_image) {
                Storage::disk('public')->delete($model->website_image);
            }
            $data['website_image'] = $request->file('website_image')->store('models/website', 'public');
        }

        $model->update($data);

        return redirect()->route('super.models.index')->with('success', 'Model updated successfully');
    }

    public function destroy($id)
    {
        $model = Models::findOrFail($id);
        
        // Delete associated images
        if ($model->scanned_image) {
            Storage::disk('public')->delete($model->scanned_image);
        }
        if ($model->website_image) {
            Storage::disk('public')->delete($model->website_image);
        }

        $model->delete();

        return redirect()->route('super.models.index')->with('success', 'Model deleted successfully');
    }
} 