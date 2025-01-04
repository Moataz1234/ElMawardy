<?php

namespace App\Http\Controllers;

use App\Models\Talabat;
use App\Models\GoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TalabatController extends Controller
{
    public function index(Request $request)
    {
        $query = Talabat::query();
    
        if ($request->has('search')) {
            $query->where('model', 'like', '%' . $request->search . '%');
        }
    
        if ($request->has('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }
    
        $talabat = $query->paginate(20);
    
        return view('admin.Gold.talabat', compact('talabat'));
    }

    public function create()
    {
        return view('admin.Gold.create_talabat');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255|unique:talabat,model',
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
            $prefix = $matches[1];
            $mainPart = $matches[2];
            $suffix = $matches[3] ?? '';
            $sku = 'G' . $prefix . $mainPart . $suffix;
        } else {
            $sku = 'G' . str_pad(substr($request->model, -4), 4, '0', STR_PAD_LEFT);
        }

        $validatedData['SKU'] = $sku;

        if ($request->hasFile('scanned_image')) {
            $scannedImagePath = $request->file('scanned_image')->store('Gold_catalog', 'public');
            $validatedData['scanned_image'] = $scannedImagePath;
        }

        if ($request->hasFile('website_image')) {
            $websiteImagePath = $request->file('website_image')->store('talabat/website', 'public');
            $validatedData['website_image'] = $websiteImagePath;
        }

        Talabat::create($validatedData);

        return redirect()->route('gold-items.create')->with('success', 'Talabat added successfully.');
    }

    public function edit(Talabat $talabat)
    {
        return view('admin.Gold.edit_talabat', compact('talabat'));
    }

    public function update(Request $request, Talabat $talabat)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255|unique:talabat,model,' . $talabat->id,
            'stars' => 'string|max:255|nullable',
            'source' => 'string|max:255|nullable',
            'scanned_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            'website_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ]);

        preg_match('/^(\d+)-(\d+)(?:-(\w+))?$/', $request->model, $matches);

        if (count($matches) >= 3) {
            $prefix = $matches[1];
            $mainPart = $matches[2];
            $suffix = $matches[3] ?? '';
            $sku = 'G' . $prefix . $mainPart . $suffix;
        } else {
            $sku = 'G' . str_pad(substr($request->model, -4), 4, '0', STR_PAD_LEFT);
        }
    
        $validatedData['SKU'] = $sku;
    
        if ($request->hasFile('scanned_image')) {
            $scannedImagePath = $request->file('scanned_image')->store('talabat/scanned', 'public');
            $validatedData['scanned_image'] = $scannedImagePath;
        }

        if ($request->hasFile('website_image')) {
            $websiteImagePath = $request->file('website_image')->store('talabat/website', 'public');
            $validatedData['website_image'] = $websiteImagePath;
        }

        $talabat->update($validatedData);

        return redirect()->route('talabat.index')->with('success', 'Talabat updated successfully.');
    }

    public function destroy(Talabat $talabat)
    {
        if ($talabat->scanned_image) {
            Storage::disk('public')->delete($talabat->scanned_image);
        }
        if ($talabat->website_image) {
            Storage::disk('public')->delete($talabat->website_image);
        }

        $talabat->delete();

        return redirect()->route('talabat.index')->with('success', 'Talabat deleted successfully.');
    }

    public function getTalabatDetails(Request $request)
    {
        $model = $request->input('model');

        $talabatDetails = Talabat::where('model', $model)->first();
    
        $items = GoldItem::with('shop')
            ->where('model', $model)
            ->whereHas('shop')
            ->get()
            ->map(function ($item) {
                return [
                    'serial_number' => $item->serial_number,
                    'shop_name' => $item->shop->name,
                    'weight' => $item->weight,
                    'gold_color' => $item->gold_color
                ];
            });
    
        return response()->json([
            'items' => $items,
            'talabatDetails' => $talabatDetails ? [
                'scanned_image' => $talabatDetails->scanned_image,
            ] : null
        ]);
    }
}
