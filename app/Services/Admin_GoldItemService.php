<?php

namespace App\Services;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\Shop;
use App\Models\AddRequest;
use App\Http\Requests\UpdateGoldItemRequest;
use App\Models\DeletedItemHistory;
use App\Models\ItemRequest;
use App\Models\User;
use App\Models\GoldItemWeightHistory;
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

    public function findGoldItem($id)
    {
        return GoldItem::findOrFail($id);
    }

    public function updateGoldItem(UpdateGoldItemRequest $request, $id)
    {
        $goldItem = $this->findGoldItem($id);
    
        // Get the weight before the update
        $weightBefore = $goldItem->weight;
    
        // Update the gold item
        $goldItem->update($request->validated());
    
        // Get the weight after the update
        $weightAfter = $goldItem->weight;
    
        // Save the weight change history
        if ($weightBefore != $weightAfter) {
            GoldItemWeightHistory::create([
                'gold_item_id' => $goldItem->id,
                'weight_before' => $weightBefore,
                'weight_after' => $weightAfter,
            ]);
        }
    
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
   

    public function generateNextSerialNumber()
    {
        // Get the highest serial number from both GoldItem and AddRequest tables
        $lastGoldItemSerial = GoldItem::orderByRaw('CAST(SUBSTRING(serial_number, 3) AS UNSIGNED) DESC')->value('serial_number');
        $lastAddRequestSerial = AddRequest::orderByRaw('CAST(SUBSTRING(serial_number, 3) AS UNSIGNED) DESC')->value('serial_number');
    
        // Extract the numeric parts
        $lastGoldItemNumber = $lastGoldItemSerial ? intval(substr($lastGoldItemSerial, 2)) : 0;
        $lastAddRequestNumber = $lastAddRequestSerial ? intval(substr($lastAddRequestSerial, 2)) : 0;
    
        // Get the next number
        $nextNumber = max($lastGoldItemNumber, $lastAddRequestNumber) + 1;
    
        // Format the next serial number
        $nextSerialNumber = 'G-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    
        // Log for debugging
        Log::info('Generated serial number', ['serial_number' => $nextSerialNumber]);
    
        return $nextSerialNumber;
    }
    
    public function getGoldItems( $request)
    { 
        $query = GoldItem::with(['shop', 'modelCategory']);
    
        // Apply search filter
        if ($search = $request->input('search')) {
            $searchType = $request->input('search_type', 'model'); // Default to 'model'
            $normalizedSearch = ltrim(preg_replace('/\D/', '', $search), '0');
    
            if ($searchType === 'serial_number') {
                $query->where('serial_number', 'like', "%{$search}%");
            } else {
                // Default to model search
                $query->where(function ($query) use ($normalizedSearch) {
                    $query->where('model', 'like', "%{$normalizedSearch}%")
                          ->orWhere('model', 'like', "%-" . substr($normalizedSearch, 1) . "%");
                });
            }
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
        $sortDirection = $request->input('direction') === 'asc' ? 'asc' : 'desc';
    
        // Step 4: Return paginated, sorted, and filtered results
        return $query->orderBy($sortField, $sortDirection)
                     ->paginate(20)
                     ->appends($request->all());
    }
    

    public function createWorkshopRequests(array $items, $reason, $transferAllModels = false)
    {
        DB::transaction(function () use ($items, $reason, $transferAllModels) {
            $user = Auth::user();
            
            // Get full item details
            $itemIds = array_column($items, 'id');
            $goldItems = GoldItem::whereIn('id', $itemIds)->get();
            
            // Group items by shop name
            $itemsByShop = $goldItems->groupBy('shop_name');
            
            foreach ($itemsByShop as $shopName => $shopItems) {
                // Find users with matching shop_name
                $shopUsers = \App\Models\User::where('shop_name', $shopName)->get();
                
                $notificationItems = [];
                
                foreach ($shopItems as $item) {
                    // Create workshop request
                    DB::table('workshop_transfer_requests')->insert([
                        'item_id' => $item->id,
                        'shop_name' => $item->shop_name,
                        'serial_number' => $item->serial_number,
                        'reason' => $reason,
                        'requested_by' => $user->name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $notificationItems[] = [
                        'id' => $item->id,
                        'serial_number' => $item->serial_number,
                        'model' => $item->model,
                        'weight' => $item->weight
                    ];
                }
                
                // Notify shop users
                foreach ($shopUsers as $shopUser) {
                    $shopUser->notify(new \App\Notifications\WorkshopTransferRequestNotification([
                        'items' => $notificationItems,
                        'reason' => $reason,
                        'requested_by' => $user->name
                    ]));
                }
            }
        });
    }

    public function bulkTransferToWorkshop(array $ids, $reason = null, $transferAllModels = false)
    {
        DB::transaction(function () use ($ids, $reason, $transferAllModels) {
            $user = Auth::user();
            
            // Get either the selected items or all items with matching models
            $items = $transferAllModels 
                ? GoldItem::whereIn('model', function($query) use ($ids) {
                    $query->select('model')
                        ->from('gold_items')
                        ->whereIn('id', $ids);
                })->get()
                : GoldItem::whereIn('id', $ids)->get();
            
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

                // Delete the original item after transferring to workshop
                $item->delete();
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
            $searchType = $request->input('search_type', 'model'); // Default to 'model'
            $normalizedSearch = ltrim(preg_replace('/\D/', '', $search), '0');
    
            if ($searchType === 'serial_number') {
                $query->where('serial_number', 'like', "%{$search}%");
            } else {
                // Default to model search
                $query->where(function ($query) use ($normalizedSearch) {
                    $query->where('model', 'like', "%{$normalizedSearch}%")
                          ->orWhere('model', 'like', "%-" . substr($normalizedSearch, 1) . "%");
                });
            }
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
