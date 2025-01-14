<?php

namespace App\Services;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\Shop;
use App\Http\Requests\UpdateGoldItemRequest;
use App\Models\DeletedItemHistory;
use App\Models\ItemRequest;
use App\Models\User;
use App\Notifications\ItemRequestNotification;
use App\Services\SortAndFilterService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class Admin_GoldItemService
{

    protected $sortAndFilterService;

    public function __construct(SortAndFilterService $sortAndFilterService)
    {
        $this->sortAndFilterService = $sortAndFilterService;

    }
    // public function createGoldItem(array $validatedData, string $imagePath = null)
    // {
    //     // Automatically generate the next serial number
    //     $lastItem = GoldItem::orderByRaw('CAST(SUBSTRING(serial_number, 3) AS UNSIGNED) DESC')->first();
    //     $nextSerialNumber = $this->generateNextSerialNumber($lastItem);

    //     GoldItem::create([
    //         'serial_number' => $nextSerialNumber,
    //         'shop_id' => $validatedData['shop_id'],
    //         'shop_name' => Shop::find($validatedData['shop_id'])->name,
    //         'kind' => $validatedData['kind'],
    //         'model' => $validatedData['model'],
    //         'gold_color' => $validatedData['gold_color'],
    //         'metal_type' => $validatedData['metal_type'],
    //         'metal_purity' => $validatedData['metal_purity'],
    //         'quantity' => $validatedData['quantity'],
    //         'weight' => $validatedData['weight'],
    //         // 'source' => $validatedData['source'],
    //         // 'link' => $imagePath,
    //     ]);
    // }
    public function findGoldItem($id)
    {
        return GoldItem::findOrFail($id);
    }

    public function updateGoldItem(UpdateGoldItemRequest $request, $id)
    {
        $goldItem = $this->findGoldItem($id);
        $goldItem->update($request->validated());
        return $goldItem;
    }
    
    
    public function bulkDelete(array $ids, $reason = null)
    {
        try {
            return DB::transaction(function () use ($ids, $reason) {
                $items = GoldItem::whereIn('id', $ids)->get();
                
                if ($items->isEmpty()) {
                    throw new \Exception('No items found to delete');
                }
                
                // Delete related transfer requests first
                DB::table('transfer_requests')->whereIn('gold_item_id', $ids)->delete();
                
                // Create history records
                foreach ($items as $item) {
                    DeletedItemHistory::create([
                        'item_id' => $item->id,
                        'deleted_by' => Auth::user()->name,
                        'serial_number' => $item->serial_number,
                        'shop_name' => $item->shop_name,
                        'kind' => $item->kind,
                        'model' => $item->model,
                        'gold_color' => $item->gold_color,
                        'metal_purity' => $item->metal_purity,
                        'weight' => $item->weight,
                        'deletion_reason' => $reason,
                        'deleted_at' => now()
                    ]);
                }
                
                // Then delete the items
                return GoldItem::whereIn('id', $ids)->delete();
            });
        } catch (\Exception $e) {
            Log::error('Bulk deletion failed: ' . $e->getMessage());
            throw $e;
        }
    }
    public function bulkRequest(array $ids)
    {
        // Get all selected items
        $items = GoldItem::whereIn('id', $ids)->get();
        
        // Group items by shop for efficient processing
        $itemsByShop = $items->groupBy('shop_name');
        
        foreach ($itemsByShop as $shopName => $shopItems) {
            // Create request record for each item
            foreach ($shopItems as $item) {
                ItemRequest::create([
                    'item_id' => $item->id,
                    'admin_id' => Auth::id(),
                    'shop_name' => $shopName,
                    'status' => 'pending'
                ]);
            }
            
            // Notify shop users
            $shopUsers = User::where('shop_name', $shopName)->get();
            foreach ($shopUsers as $user) {
                $user->notify(new ItemRequestNotification($shopItems, Auth::user()));
            }
        }
        
        // Update items status
        GoldItem::whereIn('id', $ids)->update(['status' => 'requested']);
        return true;
    }
    // public function updateGoldItem($id, array $validatedData, $file = null)
    // {
    //     $goldItem = GoldItem::findOrFail($id);

    //     if ($file) {
    //         // Store the file
    //         $imagePath = $file->store('uploads/gold_items', 'public');
    //         $validatedData['link'] = $imagePath;

    //         // Optionally delete the old image if needed
    //         if ($goldItem->link) {
    //             Storage::delete('public/' . $goldItem->link);
    //         }
    //     }

    //     // Update the GoldItem
    //     $goldItem->update($validatedData);
    // }

    public function generateNextSerialNumber()
    {
        // Fetch the last item with the highest serial number
        $lastItem = GoldItem::orderBy('serial_number', 'desc')->first();
    
        if ($lastItem) {
            // Extract the numeric part of the serial number
            $lastNumber = (int) substr($lastItem->serial_number, 2); // Assumes format "G-XXXXXX"
            $nextNumber = $lastNumber + 1;
        } else {
            // If no items exist, start from 1
            $nextNumber = 1;
        }
    
        // Format the next serial number
        $nextSerialNumber = 'G-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    
        // Log the generated serial number for debugging
        Log::info('Generated serial number', ['serial_number' => $nextSerialNumber]);
    
        return $nextSerialNumber;
    }
    public function getGoldItems( $request)
{ 
    $query = GoldItem::with(['shop', 'modelCategory']);

    // Apply search filter
    if ($search = $request->input('search')) {
        // Normalize search input by removing non-numeric characters and leading zeros
        $normalizedSearch = ltrim(preg_replace('/\D/', '', $search), '0');

        $query->where(function ($query) use ($normalizedSearch) {
            $query->where('model', 'like', "%{$normalizedSearch}%")
                ->orWhere('model', 'like', "%-" . substr($normalizedSearch, 1) . "%"); // Handles "1-0010" pattern
        });
    }
    // Apply filters for metal purity and kind
    if ($metalPurity = $request->input('metal_purity')) {
        $query->whereIn('metal_purity', $metalPurity);
    }

    if ($kind = $request->input('kind')) {
        $query->whereIn('kind', $kind);
    }
    if ($category = $request->input('category')) {
        $query->whereHas('modelCategory', function ($q) use ($category) {
            $q->whereIn('category', $category);
        });
    }
    if ($shopName  = $request->input('shop_name')) {
        $query->whereIn('shop_name', $shopName);
    }
    $sortableFields = ['serial_number', 'model', 'kind', 'quantity', 'created_at'];
    $sortField = in_array($request->input('sort'), $sortableFields) ? $request->input('sort') : 'serial_number';
    $sortDirection = $request->input('direction') === 'desc' ? 'desc' : 'asc';

    // Step 4: Return paginated, sorted, and filtered results
    return $query->orderBy($sortField, $sortDirection)
                 ->paginate(20)
                 ->appends($request->all());
}

    public function bulkTransferToWorkshop(array $ids, $reason = null)
    {
        DB::transaction(function () use ($ids, $reason) {
            $items = GoldItem::whereIn('id', $ids)->get();
            $user = auth()->user();
            
            foreach ($items as $item) {
                // Create workshop record
                DB::table('workshop_items')->insert([
                    'item_id' => $item->id,
                    'transferred_by' => $user->name,
                    'serial_number' => $item->serial_number,
                    'shop_name' => $item->shop_name,
                    'kind' => $item->kind,
                    'model' => $item->model,
                    'gold_color' => $item->gold_color,
                    'metal_purity' => $item->metal_purity,
                    'weight' => $item->weight,
                    'transfer_reason' => $reason,
                    'transferred_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update original item status
                $item->update(['status' => 'in_workshop']);
            }
        });
    }

    public function getGoldItemsSold($request)
    {
        $query = GoldItemSold::query();

        // $userShopName = Auth::user()->shop_name;
        
        // Filter by user's shop name
        // $query->where('shop_name', $userShopName);
        // Apply search filter
        if ($search = $request->input('search')) {
            $normalizedSearch = ltrim(preg_replace('/\D/', '', $search), '0');

            $query->where(function ($query) use ($normalizedSearch) {
                $query->where('model', 'like', "%{$normalizedSearch}%")
                    ->orWhere('model', 'like', "%-" . substr($normalizedSearch, 1) . "%");
            });
        }

        // Apply filters
        if ($goldColor = $request->input('gold_color')) {
            $query->whereIn('gold_color', $goldColor);
        }

        if ($kind = $request->input('kind')) {
            $query->whereIn('kind', $kind);
        }

        if ($shopName = $request->input('shop_name')) {
            $query->whereIn('shop_name', $shopName);
        }

        // Define sortable fields
        $sortableFields = ['serial_number', 'model', 'kind', 'quantity', 'sold_date'];
        $sortField = in_array($request->input('sort'), $sortableFields) 
            ? $request->input('sort') 
            : 'serial_number';
        $sortDirection = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortField, $sortDirection)
                    ->paginate(20)
                    ->appends($request->all());
    }
}
