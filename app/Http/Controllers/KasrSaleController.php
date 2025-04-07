<?php

namespace App\Http\Controllers;

use App\Models\KasrSale;
use App\Models\KasrItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class KasrSaleController extends Controller
{
    public function index()
    {
        $kasrSales = KasrSale::with('items')
            ->where('shop_name', Auth::user()->shop_name)
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
            'customer_phone' => 'nullable|string|max:20',
            'offered_price' => 'nullable|numeric|min:0',
            'order_date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            
            // Item fields (as array of items)
            'items' => 'required|array',
            'items.*.kind' => 'required|string|max:255',
            'items.*.metal_purity' => 'required|string|max:255',
            'items.*.weight' => 'required|numeric|min:0',
            'items.*.net_weight' => 'nullable|numeric|min:0',
            'items.*.item_type' => 'nullable|string|in:shop',
        ]);

        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Create the main sale record
            $kasrSale = new KasrSale();
            $kasrSale->customer_name = $validated['customer_name'];
            $kasrSale->customer_phone = $validated['customer_phone'];
            $kasrSale->shop_name = Auth::user()->shop_name;
            $kasrSale->offered_price = $validated['offered_price'];
            $kasrSale->order_date = $validated['order_date'] ?? now();
            
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('kasr_sales', 'public');
                $kasrSale->image_path = $path;
            }
            
            $kasrSale->save();
            
            // Create the item records
            foreach ($validated['items'] as $itemData) {
                $kasrItem = new KasrItem();
                $kasrItem->kasr_sale_id = $kasrSale->id;
                $kasrItem->kind = $itemData['kind'];
                $kasrItem->metal_purity = $itemData['metal_purity'];
                $kasrItem->weight = $itemData['weight'];
                $kasrItem->net_weight = $itemData['net_weight'] ?? null;
                
                // Set item_type based on the individual checkbox
                $kasrItem->item_type = isset($itemData['item_type']) ? 'shop' : 'customer';
                
                $kasrItem->save();
            }
            
            DB::commit();
            
            return redirect()->route('kasr-sales')
                ->with('success', 'تم اضافة الكسر بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage())->withInput();
        }
    }

    public function show(KasrSale $kasrSale)
    {
        $kasrSale->load('items');
        return view('kasr_sales.show', compact('kasrSale'));
    }

    public function edit(KasrSale $kasrSale)
    {
        $kasrSale->load('items');
        return view('kasr_sales.edit', compact('kasrSale'));
    }

    public function update(Request $request, KasrSale $kasrSale)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'offered_price' => 'nullable|numeric|min:0',
            'order_date' => 'nullable|date',
            'status' => 'nullable|string|in:pending,accepted,rejected',
            'image' => 'nullable|image|max:2048',
            'item_type' => 'nullable|string|max:255',
            
            // Item fields (for existing items)
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:kasr_items,id',
            'items.*.kind' => 'required|string|max:255',
            'items.*.metal_purity' => 'required|string|max:255',
            'items.*.weight' => 'required|numeric|min:0',
            'items.*.net_weight' => 'nullable|numeric|min:0',
            
            // New item fields
            'new_items' => 'nullable|array',
            'new_items.*.kind' => 'required|string|max:255',
            'new_items.*.metal_purity' => 'required|string|max:255',
            'new_items.*.weight' => 'required|numeric|min:0',
            'new_items.*.net_weight' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Update the main sale record
            $kasrSale->customer_name = $validated['customer_name'];
            $kasrSale->customer_phone = $validated['customer_phone'];
            $kasrSale->offered_price = $validated['offered_price'] ?? null;
            $kasrSale->order_date = $validated['order_date'] ?? null;
            $kasrSale->status = $validated['status'] ?? $kasrSale->status;
            $kasrSale->item_type = $request->has('item_type') ? 'shop' : 'customer';
            
            if ($request->hasFile('image')) {
                if ($kasrSale->image_path) {
                    Storage::disk('public')->delete($kasrSale->image_path);
                }
                
                $path = $request->file('image')->store('kasr_sales', 'public');
                $kasrSale->image_path = $path;
            }
            
            $kasrSale->save();
            
            // Update existing items
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    $item = KasrItem::find($itemData['id']);
                    if ($item && $item->kasr_sale_id == $kasrSale->id) {
                        $item->kind = $itemData['kind'];
                        $item->metal_purity = $itemData['metal_purity'];
                        $item->weight = $itemData['weight'];
                        $item->net_weight = $itemData['net_weight'] ?? null;
                        $item->save();
                    }
                }
            }
            
            // Add new items
            if (isset($validated['new_items'])) {
                foreach ($validated['new_items'] as $newItemData) {
                    $newItem = new KasrItem();
                    $newItem->kasr_sale_id = $kasrSale->id;
                    $newItem->kind = $newItemData['kind'];
                    $newItem->metal_purity = $newItemData['metal_purity'];
                    $newItem->weight = $newItemData['weight'];
                    $newItem->net_weight = $newItemData['net_weight'] ?? null;
                    $newItem->save();
                }
            }
            
            DB::commit();
            
            return redirect()->route('kasr-sales.index')
                ->with('success', 'تم تعديل الكسر بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(KasrSale $kasrSale)
    {
        if ($kasrSale->image_path) {
            Storage::disk('public')->delete($kasrSale->image_path);
        }
        
        // Items will be automatically deleted due to the cascade constraint
        $kasrSale->delete();
        
        return redirect()->route('kasr-sales.index')
            ->with('success', 'تم حذف الكسر بنجاح.');
    }
} 