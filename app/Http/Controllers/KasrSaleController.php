<?php

namespace App\Http\Controllers;

use App\Models\KasrSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KasrSaleController extends Controller
{
    public function index()
    {
        $kasrSales = KasrSale::where('shop_name', Auth::user()->shop_name)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('kasr_sales.index', compact('kasrSales'));
    }

    public function create()
    {
        return view('kasr_sales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'metal_purity' => 'required|string|max:255',
            'metal_type' => 'nullable|string|max:255',
            'offered_price' => 'nullable|numeric|min:0',
            'order_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'kind' => 'nullable|string|max:255',
        ]);

        $kasrSale = new KasrSale();
        $kasrSale->customer_name = $validated['customer_name'];
        $kasrSale->shop_name = Auth::user()->shop_name;
        $kasrSale->weight = $validated['weight'];
        $kasrSale->metal_purity = $validated['metal_purity'];
        // $kasrSale->metal_type = $validated['metal_type'] ?? 'gold';
        $kasrSale->offered_price = $validated['offered_price'];
        $kasrSale->order_date = $validated['order_date'] ?? now();
        // $kasrSale->notes = $validated['notes'] ?? null;
        $kasrSale->kind = $validated['kind'] ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('kasr_sales', 'public');
            $kasrSale->image_path = $path;
        }
        
        $kasrSale->save();
        
        return redirect()->route('gold-items.index')
            ->with('success', 'تم اضافة الكسر بنجاح.');
    }

    public function show(KasrSale $kasrSale)
    {
        return view('kasr_sales.show', compact('kasrSale'));
    }

    public function edit(KasrSale $kasrSale)
    {
        return view('kasr_sales.edit', compact('kasrSale'));
    }

    public function update(Request $request, KasrSale $kasrSale)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'metal_purity' => 'required|string|max:255',
            'metal_type' => 'nullable|string|max:255',
            'offered_price' => 'nullable|numeric|min:0',
            'order_date' => 'nullable|date',
            'status' => 'nullable|string|in:pending,accepted,rejected',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $kasrSale->customer_name = $validated['customer_name'];
        $kasrSale->weight = $validated['weight'];
        $kasrSale->metal_purity = $validated['metal_purity'];
        $kasrSale->metal_type = $validated['metal_type'] ?? 'gold' ?? null;
        $kasrSale->offered_price = $validated['offered_price'] ?? null;
        $kasrSale->order_date = $validated['order_date'] ?? null;
        $kasrSale->status = $validated['status'] ?? $kasrSale->status;
        $kasrSale->notes = $validated['notes'] ?? null;
        
        if ($request->hasFile('image')) {
            if ($kasrSale->image_path) {
                Storage::disk('public')->delete($kasrSale->image_path);
            }
            
            $path = $request->file('image')->store('kasr_sales', 'public');
            $kasrSale->image_path = $path;
        }
        
        $kasrSale->save();
        
        return redirect()->route('kasr-sales.index')
            ->with('success', 'تم تعديل الكسر بنجاح.');
    }

    public function destroy(KasrSale $kasrSale)
    {
        if ($kasrSale->image_path) {
            Storage::disk('public')->delete($kasrSale->image_path);
        }
        
        $kasrSale->delete();
        
        return redirect()->route('kasr-sales.index')
            ->with('success', 'Kasr sale record deleted successfully.');
    }
} 