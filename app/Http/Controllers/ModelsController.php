<?php

namespace App\Http\Controllers;

use App\Models\Models;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelsController extends Controller
{
    public function index(Request $request)
    {
        $query = Models::query();
    
        if ($request->has('search')) {
            $query->where('model', 'like', '%' . $request->search . '%');
        }
    
        if ($request->has('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }
    
        $models = $query->paginate(20); // Change from get() to paginate(20)
    
        return view('admin.Gold.models', compact('models'));
    }

    public function create()
    {
        return view('admin.Gold.create_model');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255|unique:models,model',
            'category' => 'string|max:255|nullable',
            'source' => 'string|max:255|nullable',
            'first_production' => 'date|nullable',
            'semi_or_no' => 'string|max:255|nullable',
            'average_of_stones' => 'numeric|nullable',
            'scanned_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            'website_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ]);

        // Generate SKU from model number
        preg_match('/^(\d+)-(\d+)(?:-(\w+))?$/', $request->model, $matches);

    if (count($matches) >= 3) {
        $prefix = $matches[1]; // e.g., 1
        $mainPart = $matches[2]; // e.g., 0003
        $suffix = $matches[3] ?? ''; // e.g., A or B (optional)
        $sku = 'G' . $prefix . $mainPart . $suffix; // Combine parts
    } else {
        // Default SKU in case of invalid format
        $sku = 'G' . str_pad(substr($request->model, -4), 4, '0', STR_PAD_LEFT);
    }

    $validatedData['SKU'] = $sku;

        // Handle scanned image upload
        if ($request->hasFile('scanned_image')) {
            $scannedImagePath = $request->file('scanned_image')->store('Gold_catalog', 'public');
            $validatedData['scanned_image'] = $scannedImagePath;
        }

        // Handle website image upload
        if ($request->hasFile('website_image')) {
            $websiteImagePath = $request->file('website_image')->store('models/website', 'public');
            $validatedData['website_image'] = $websiteImagePath;
        }

        Models::create($validatedData);

        return redirect()->route('models.index')->with('success', 'Model added successfully.');
    }

    public function edit(Models $model)
    {
        return view('admin.Gold.edit_model', compact('model'));
    }

    public function update(Request $request, Models $model)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255|unique:models,model,' . $model->id,
            'category' => 'string|max:255|nullable',
            'source' => 'string|max:255|nullable',
            // 'first_production' => 'date|nullable',
            // 'semi_or_no' => 'string|max:255|nullable',
            // 'average_of_stones' => 'numeric|nullable',
            'scanned_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            'website_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ]);

        preg_match('/^(\d+)-(\d+)(?:-(\w+))?$/', $request->model, $matches);

        if (count($matches) >= 3) {
            $prefix = $matches[1]; // e.g., 1
            $mainPart = $matches[2]; // e.g., 0003
            $suffix = $matches[3] ?? ''; // e.g., A or B (optional)
            $sku = 'G' . $prefix . $mainPart . $suffix; // Combine parts
        } else {
            // Default SKU in case of invalid format
            $sku = 'G' . str_pad(substr($request->model, -4), 4, '0', STR_PAD_LEFT);
        }
    
        $validatedData['SKU'] = $sku;
    

        // Handle scanned image upload
        if ($request->hasFile('scanned_image')) {
            // Delete old image if exists
            if ($model->scanned_image) {
                Storage::disk('public')->delete($model->scanned_image);
            }
            $scannedImagePath = $request->file('scanned_image')->store('models/scanned', 'public');
            $validatedData['scanned_image'] = $scannedImagePath;
        }

        // Handle website image upload
        if ($request->hasFile('website_image')) {
            // Delete old image if exists
            if ($model->website_image) {
                Storage::disk('public')->delete($model->website_image);
            }
            $websiteImagePath = $request->file('website_image')->store('models/website', 'public');
            $validatedData['website_image'] = $websiteImagePath;
        }

        $model->update($validatedData);

        return redirect()->route('models.index')->with('success', 'Model updated successfully.');
    }

    public function destroy(Models $model)
    {
        // Delete associated images
        if ($model->scanned_image) {
            Storage::disk('public')->delete($model->scanned_image);
        }
        if ($model->website_image) {
            Storage::disk('public')->delete($model->website_image);
        }

        $model->delete();

        return redirect()->route('models.index')->with('success', 'Model deleted successfully.');
    }
}
