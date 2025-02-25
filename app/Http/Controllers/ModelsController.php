<?php

namespace App\Http\Controllers;

use App\Models\Models;
use App\Models\GoldItem;
use App\Models\Talabat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;


class ModelsController extends Controller
{
    public function index(Request $request)
    {
        $query = Models::query();

        // Apply search filter if the 'search' parameter is present
        if ($request->has('search')) {
            $query->where('model', 'like', '%' . $request->search . '%');
        }

        // Apply sorting if the 'sort' parameter is present
        if ($request->has('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }

        // Check if the 'talabat' tab is active
        if ($request->has('tab') && $request->tab === 'talabat') {
            $query->where('model', 'like', '%T%'); // Show only models with 'T' in their names
        } else {
            $query->where('model', 'not like', '%T%'); // Exclude models with 'T' in their names
        }

        $models = $query->paginate(20);

        return view('admin.Gold.Models.models', compact('models'));
    }

    public function create(Request  $request)
    {
        $model = $request->query('model');

        return view('admin.Gold.Models.create_model',['model' => $model]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255|unique:models,model',
            'stars' => 'string|max:255|nullable',
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

        return redirect()->route('gold-items.create')->with('success', 'Model added successfully.');
    }

    public function edit(Models $model)
    {
        return view('admin.Gold.Models.edit_model', compact('model'));
    }

    public function update(Request $request, Models $model)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255|unique:models,model,' . $model->id,
            'stars' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'first_production' => 'nullable|date',
            'semi_or_no' => 'nullable|string|max:255',
            'scanned_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Store old model name
            $oldModelName = $model->model;
            $newModelName = $validatedData['model'];

            Log::info("Attempting to update model", [
                'old_name' => $oldModelName,
                'new_name' => $newModelName,
                'validated_data' => $validatedData
            ]);

            // Handle image uploads
            if ($request->hasFile('scanned_image')) {
                $scannedImagePath = $request->file('scanned_image')->store('models/scanned', 'public');
                $validatedData['scanned_image'] = $scannedImagePath;
            }

            if ($request->hasFile('website_image')) {
                $websiteImagePath = $request->file('website_image')->store('models/website', 'public');
                $validatedData['website_image'] = $websiteImagePath;
            }

            // Update SKU if model name changed
            if ($oldModelName !== $newModelName) {
                preg_match('/^(\d+)-(\d+)(?:-(\w+))?$/', $newModelName, $matches);
                if (count($matches) >= 3) {
                    $prefix = $matches[1];
                    $mainPart = $matches[2];
                    $suffix = $matches[3] ?? '';
                    $validatedData['SKU'] = 'G' . $prefix . $mainPart . $suffix;
                } else {
                    $validatedData['SKU'] = 'G' . str_pad(substr($newModelName, -4), 4, '0', STR_PAD_LEFT);
                }
            }

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Update the model
            $updated = DB::table('models')
                ->where('id', $model->id)
                ->update($validatedData);

            Log::info("Model update result", ['success' => $updated]);

            // If model name is changing and model update was successful, update related tables
            if ($updated && $oldModelName !== $newModelName) {
                $tables = [
                    'gold_items',
                    'add_requests',
                    'warehouses',
                    'deleted_items_history',
                    'gold_items_avg',
                    'gold_items_sold',
                    'workshop_items'
                ];

                foreach ($tables as $table) {
                    if (Schema::hasTable($table)) {
                        $updated = DB::table($table)
                            ->where('model', $oldModelName)
                            ->update(['model' => $newModelName]);
                        
                        Log::info("Updated table {$table}", ['rows_affected' => $updated]);
                    }
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            DB::commit();
            return redirect()->route('models.index')->with('success', 'Model updated successfully.');

        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            DB::rollBack();
            Log::error("Error updating model: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating model: ' . $e->getMessage())
                ->withInput();
        }
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
    public function getModelDetails(Request $request)
    {
        $model = $request->input('model');
        $isTalabat = $request->boolean('is_talabat');

        try {
            $items = GoldItem::where($isTalabat ? 'talabat' : 'model', $model)->get();

            if ($isTalabat) {
                $talabatDetails = Talabat::where('model', $model)->first();
                return response()->json([
                    'talabatDetails' => $talabatDetails,
                    'items' => $items
                ]);
            } else {
                $modelDetails = Models::where('model', $model)->first();
                return response()->json([
                    'modelDetails' => $modelDetails,
                    'items' => $items
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching model details: ' . $e->getMessage()
            ], 500);
        }
    }
    public function generateModel(Request $request)
{
    $prefix = $request->query('prefix');
    $isTalabat = $request->query('talabat') === 'true'; // Ensure boolean value

    // Query to find the last model number based on the prefix
    $query = Models::where('model', 'like', $prefix . '-%');

    // Apply Talabat condition
    if ($isTalabat) {
        $query->where('model', 'like', '%T'); // Include "T" for Talabat
    } else {
        $query->where('model', 'not like', '%T'); // Exclude "T" for non-Talabat
    }

    $lastModel = $query->orderBy('model', 'desc')->first();

    if ($lastModel) {
        return response()->json(['model' => $lastModel->model]);
    }

    return response()->json(['model' => null]);
}
public function checkModelExists(Request $request)
{
    $model = $request->query('model');

    $exists = Models::where('model', $model)->exists();

    return response()->json(['exists' => $exists]);
}
}
