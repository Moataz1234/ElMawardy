<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoldItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\TransferRequest;
use App\Models\TransferRequestHistory;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TransferRequestNotification;


class DidItemsController extends Controller
{
    /**
     * Display a listing of all workshop items.
     */
    public function workshopItems(Request $request)
    {
        $workshopItems = DB::table('workshop_items')
            ->orderBy('transferred_at', 'desc')
            ->paginate(20);

        return view('admin.Gold.workshop_items', compact('workshopItems'));
    }

    /**
     * Display a listing of workshop transfer requests for admin.
     */
    public function workshopRequests(Request $request)
    {
        $query = DB::table('workshop_transfer_requests');
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            // Default to pending if no status is specified
            $query->where('status', 'pending');
        }
        
        if ($request->has('shop_name') && $request->shop_name != '') {
            $query->where('shop_name', $request->shop_name);
        }
        
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        
        // Get all shop names for the filter dropdown
        $shops = DB::table('workshop_transfer_requests')
            ->select('shop_name')
            ->distinct()
            ->pluck('shop_name');
            
        $requests = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.requests.workshop_requests', compact('requests', 'shops'));
    }

    /**
     * Display workshop requests for the authenticated shop.
     */
    public function shopWorkshopRequests(Request $request)
    {
        // Get the authenticated user's shop name
        $shopName = Auth::user()->shop_name;
        
        // Debug info - let's log what we're looking for
        Log::info('Shop workshop requests being checked for shop: ' . $shopName);
        
        // First, check if there are any requests for this exact shop name
        $exactMatchCount = DB::table('workshop_transfer_requests')
            ->where('shop_name', $shopName)
            ->where('status', 'pending')
            ->count();
            
        Log::info('Found ' . $exactMatchCount . ' exact matches for shop name: ' . $shopName);
        
        // Get all unique shop names in the system for comparison
        $allShopNames = DB::table('workshop_transfer_requests')
            ->select('shop_name')
            ->distinct()
            ->pluck('shop_name')
            ->toArray();
            
        Log::info('All shop names in workshop_transfer_requests: ' . implode(', ', $allShopNames));
        
        // Query with the authenticated user's shop name
        $query = DB::table('workshop_transfer_requests')
            ->where('shop_name', $shopName);
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', '%' . $search . '%')
                  ->orWhere('reason', 'like', '%' . $search . '%')
                  ->orWhere('requested_by', 'like', '%' . $search . '%');
            });
        }
        
        $requests = $query->orderBy('created_at', 'desc')
            ->where('status', 'pending')
            ->paginate(20);
            
        // Log the count of requests found
        Log::info('Found ' . $requests->total() . ' requests for shop: ' . $shopName);
        
        // For debugging purposes, if no requests are found with the exact name,
        // show a message to the user about possible shop name mismatch
        $allShops = $allShopNames;
        $noRequestsFound = $requests->total() === 0;

        return view('shops.workshop_requests', compact('requests', 'allShops', 'noRequestsFound', 'shopName'));
    }

    /**
     * Handle shop workshop requests batch action.
     */
    public function handleShopWorkshopRequests(Request $request)
    {
        try {
            if (!$request->has('selected_items') || empty($request->selected_items)) {
                return redirect()->back()->with('error', 'No items selected');
            }
            
            DB::beginTransaction();
            
            $shopName = Auth::user()->shop_name;
            $action = $request->input('action');
            $selectedItems = $request->input('selected_items');
            
            foreach ($selectedItems as $id) {
                $transferRequest = DB::table('workshop_transfer_requests')
                    ->where('id', $id)
                    ->where('shop_name', $shopName) // Ensure the request belongs to this shop
                    ->first();
                
                if (!$transferRequest) {
                    continue; // Skip if request not found or not belonging to shop
                }
                
                // Update request status based on action
                $status = ($action === 'accept') ? 'accepted_by_shop' : 'rejected_by_shop';
                
                DB::table('workshop_transfer_requests')
                    ->where('id', $id)
                    ->update([
                        'status' => $status,
                        'updated_at' => now()
                    ]);
                
                // Get the gold item
                $goldItem = GoldItem::find($transferRequest->item_id);
                if ($goldItem) {
                    if ($action === 'accept') {
                        // When accepted, update shop_name to Rabea
                        $goldItem->shop_name = 'Rabea';
                        $goldItem->status = 'pending_workshop';
                        $goldItem->save();
                    } else {
                        // If rejected, reset the status back to normal
                        $goldItem->status = null;
                        $goldItem->save();
                    }
                }
            }
            
            DB::commit();
            
            $message = ($action === 'accept') 
                ? 'Selected workshop requests accepted successfully' 
                : 'Selected workshop requests rejected successfully';
                
            return redirect()->route('shop.workshop.requests')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle workshop requests: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Handle individual workshop request for admin.
     */
    public function handleWorkshopRequest(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transferRequest = DB::table('workshop_transfer_requests')
                ->where('id', $id)
                ->first();

            if (!$transferRequest) {
                throw new \Exception('Transfer request not found');
            }

            // Check if the request has been previously accepted by the shop
            $wasAcceptedByShop = $transferRequest->status === 'accepted_by_shop';
            
            // Get the gold item
            $goldItem = GoldItem::find($transferRequest->item_id);
            
            if (!$goldItem) {
                throw new \Exception('Gold item not found. It may have been deleted already.');
            }

            // Handle based on the action
            if ($request->status === 'approved') {
                // Create workshop record
                DB::table('workshop_items')->insert([
                    'item_id' => $goldItem->id,
                    'transferred_by' => $transferRequest->requested_by,
                    'serial_number' => $goldItem->serial_number,
                    'shop_name' => $goldItem->shop_name,
                    'kind' => $goldItem->kind,
                    'model' => $goldItem->model,
                    'gold_color' => $goldItem->gold_color,
                    'metal_purity' => $goldItem->metal_purity,
                    'weight' => $goldItem->weight,
                    'transfer_reason' => $transferRequest->reason,
                    'transferred_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Delete the item from gold_items table
                $goldItem->delete();
            } 
            else if ($request->status === 'return_to_shop') {
                // Return the item to the shop, remove the pending_kasr status
                $goldItem->status = null;
                $goldItem->save();
            }
            else if ($request->status === 'rejected') {
                // Reset the item status
                $goldItem->status = null;
                $goldItem->save();
            }

            // Update request status
            DB::table('workshop_transfer_requests')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            DB::commit();
            
            $successMessage = $wasAcceptedByShop ? 
                'Workshop request from shop updated successfully' : 
                'Workshop request updated successfully';
                
            return redirect()->route('admin.inventory')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Workshop request handling failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create workshop transfer requests
     */
    public function createWorkshopRequests(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|string', // JSON string of item IDs
                'transfer_reason' => 'required|string',
                'transfer_all_models' => 'nullable|string'
            ]);

            // Debug received data
            Log::info('Workshop request received', [
                'items' => $request->input('items'),
                'reason' => $request->input('transfer_reason'),
                'transferAllModels' => $request->input('transfer_all_models') 
            ]);

            DB::beginTransaction(); // Start transaction

            // Decode the JSON items
            $items = json_decode($request->input('items'), true);
            
            // Debug decoded items
            Log::info('Decoded items', ['count' => count($items), 'data' => $items]);
            
            if (empty($items)) {
                Log::error('No items to transfer after JSON decoding');
                return response()->json([
                    'success' => false,
                    'message' => 'No valid items to transfer. Please check your selection.'
                ], 422);
            }
            
            $reason = $request->input('transfer_reason');
            $transferAllModels = $request->input('transfer_all_models') === 'true';

            $validItemsCount = 0;

            // Process only the selected items from the table
            foreach ($items as $item) {
                try {
                    // Make sure we're using the 'id' field from the JSON object
                    $itemId = $item['id'];
                    $goldItem = GoldItem::findOrFail($itemId);
                    
                    // Check if item already has a pending request
                    if ($goldItem->status === 'pending_kasr') {
                        Log::info('Skipping item already pending', ['id' => $goldItem->id, 'serial' => $goldItem->serial_number]);
                        continue;
                    }
                    
                    // Create workshop transfer request
                    DB::table('workshop_transfer_requests')->insert([
                        'item_id' => $goldItem->id,
                        'shop_name' => $goldItem->shop_name,  // Use the original shop name
                        'serial_number' => $goldItem->serial_number,
                        'status' => 'pending',
                        'reason' => $reason,
                        'requested_by' => Auth::user()->shop_name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Update gold item status to pending_kasr
                    $goldItem->status = 'pending_kasr';
                    $goldItem->save();
                    $validItemsCount++;
                } catch (\Exception $e) {
                    Log::warning('Error processing item', ['id' => $item['id'] ?? 'unknown', 'error' => $e->getMessage()]);
                    continue;
                }
            }
            
            Log::info('Valid items processed count', ['count' => $validItemsCount]);
            
            if ($validItemsCount === 0) {
                DB::rollBack();
                Log::error('No valid items were processed');
                return response()->json([
                    'success' => false,
                    'message' => 'No valid items to transfer. All selected items may already be in process or have invalid status.'
                ], 422);
            }

            DB::commit();

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Workshop transfer requests created successfully for ' . $validItemsCount . ' items'
                ]);
            }

            return redirect()->route('admin.inventory')->with('success', 'Workshop transfer requests created successfully for ' . $validItemsCount . ' items');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create workshop requests: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create workshop requests: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Failed to create workshop requests: ' . $e->getMessage());
        }
    }

    /**
     * Display the did_requests view for the Rabea shop
     */
    public function didRequests(Request $request)
    {
        $query = DB::table('workshop_transfer_requests');
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            // Default to showing items with 'accepted_by_shop' status
            $query->where('status', 'accepted_by_shop');
        }
        
        if ($request->has('shop_name') && $request->shop_name != '') {
            $query->where('shop_name', $request->shop_name);
        }
        
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', '%' . $search . '%')
                  ->orWhere('reason', 'like', '%' . $search . '%')
                  ->orWhere('requested_by', 'like', '%' . $search . '%');
            });
        }
        
        // Get all shop names for the filter dropdown
        $shops = DB::table('workshop_transfer_requests')
            ->select('shop_name')
            ->distinct()
            ->pluck('shop_name');
            
        $requests = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('Rabea.did_requests', compact('requests', 'shops'));
    }

    /**
     * Handle batch actions for the did_requests
     */
    public function handleDidRequests(Request $request)
    {
        try {
            if (!$request->has('selected_items') || empty($request->selected_items)) {
                return redirect()->back()->with('error', 'No items selected');
            }
            
            DB::beginTransaction();
            
            $action = $request->input('action');
            $selectedItems = $request->input('selected_items');
            
            foreach ($selectedItems as $id) {
                $transferRequest = DB::table('workshop_transfer_requests')
                    ->where('id', $id)
                    ->first();
                
                if (!$transferRequest) {
                    continue; // Skip if request not found
                }
                
                // Get the gold item
                $goldItem = GoldItem::find($transferRequest->item_id);
                if (!$goldItem) {
                    continue; // Skip if gold item not found
                }
                
                // Update the request and item based on the action
                if ($action === 'approve') {
                    // Create workshop record
                    DB::table('workshop_items')->insert([
                        'item_id' => $goldItem->id,
                        'transferred_by' => $transferRequest->requested_by,
                        'serial_number' => $goldItem->serial_number,
                        'shop_name' => $goldItem->shop_name, // Keep original shop name
                        'kind' => $goldItem->kind,
                        'model' => $goldItem->model,
                        'gold_color' => $goldItem->gold_color,
                        'metal_purity' => $goldItem->metal_purity,
                        'weight' => $goldItem->weight,
                        'transfer_reason' => $transferRequest->reason,
                        'transferred_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Delete the item from gold_items table
                    $goldItem->delete();
                    
                    // Update request status
                    DB::table('workshop_transfer_requests')
                        ->where('id', $id)
                        ->update([
                            'status' => 'approved',
                            'updated_at' => now()
                        ]);
                } 
                else if ($action === 'reject') {
                    // Reset the item status
                    $goldItem->status = null;
                    $goldItem->save();
                    
                    // Update request status
                    DB::table('workshop_transfer_requests')
                        ->where('id', $id)
                        ->update([
                            'status' => 'rejected',
                            'updated_at' => now()
                        ]);
                }
                else if ($action === 'return') {
                    // Return the item to the shop
                    $goldItem->status = null;
                    $goldItem->save();
                    
                    // Update request status
                    DB::table('workshop_transfer_requests')
                        ->where('id', $id)
                        ->update([
                            'status' => 'return_to_shop',
                            'updated_at' => now()
                        ]);
                }
            }
            
            DB::commit();
            
            $message = ($action === 'approve') 
                ? 'Selected items approved and transferred to workshop successfully' 
                : (($action === 'reject') 
                    ? 'Selected items rejected successfully' 
                    : 'Selected items returned to shop successfully');
                
            return redirect()->route('admin.inventory')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle did requests: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

/**
 * Handle DID requests from Rabea shop
 * This method processes items to be sent directly to workshop
 */
public function handleRabeaDIDRequests(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'items' => 'required|string', // JSON string of item details
            'transfer_reason' => 'required|string',
            'action' => 'required|string|in:approve,reject'
        ]);

        // Debug received data
        Log::info('DID request received', [
            'items' => $request->input('items'),
            'reason' => $request->input('transfer_reason'),
            'action' => $request->input('action')
        ]);

        DB::beginTransaction(); // Start transaction

        // Decode the JSON items
        $items = json_decode($request->input('items'), true);
        
        // Debug decoded items
        Log::info('Decoded items for DID', ['count' => count($items), 'data' => $items]);
        
        if (empty($items)) {
            Log::error('No items to transfer after JSON decoding');
            return redirect()->back()->with('error', 'No valid items to transfer. Please check your selection.');
        }
        
        $reason = $request->input('transfer_reason');
        $action = $request->input('action');
        $validItemsCount = 0;

        // Process only the selected items from the table
        foreach ($items as $item) {
            try {
                // Get item ID from the JSON object
                $itemId = $item['id'];
                $goldItem = GoldItem::findOrFail($itemId);
                
                // Check if item already has a pending request
                if ($goldItem->status === 'pending_kasr' || $goldItem->status === 'pending_workshop') {
                    Log::info('Skipping item already pending', ['id' => $goldItem->id, 'serial' => $goldItem->serial_number]);
                    continue;
                }
                
                if ($action === 'approve') {
                    // Create workshop item record
                    DB::table('workshop_items')->insert([
                        'item_id' => $goldItem->id,
                        'transferred_by' => Auth::user()->name,
                        'serial_number' => $goldItem->serial_number,
                        'shop_name' => $goldItem->shop_name,
                        'kind' => $goldItem->kind,
                        'model' => $goldItem->model,
                        'gold_color' => $goldItem->gold_color,
                        'metal_purity' => $goldItem->metal_purity,
                        'weight' => $goldItem->weight,
                        'transfer_reason' => $reason,
                        'transferred_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Create a record in workshop_transfer_requests table for tracking
                    DB::table('workshop_transfer_requests')->insert([
                        'item_id' => $goldItem->id,
                        'shop_name' => $goldItem->shop_name,
                        'serial_number' => $goldItem->serial_number,
                        'status' => 'approved',
                        'reason' => $reason,
                        'requested_by' => Auth::user()->shop_name,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Remove the item from gold_items table
                    $goldItem->delete();
                }
                
                $validItemsCount++;
            } catch (\Exception $e) {
                Log::warning('Error processing DID item', ['id' => $item['id'] ?? 'unknown', 'error' => $e->getMessage()]);
                continue;
            }
        }
        
        Log::info('Valid items processed count for DID', ['count' => $validItemsCount]);
        
        if ($validItemsCount === 0) {
            DB::rollBack();
            Log::error('No valid items were processed for DID');
            return redirect()->back()->with('error', 'No valid items to transfer. All selected items may already be in process or have invalid status.');
        }

        DB::commit();

        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Items transferred to workshop successfully (' . $validItemsCount . ' items)'
            ]);
        }

        return redirect()->route('rabea.items')->with('success', 'Items transferred to workshop successfully (' . $validItemsCount . ' items)');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to process DID request: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process DID request: ' . $e->getMessage()
            ], 422);
        }
        
        return redirect()->back()->with('error', 'Failed to process DID request: ' . $e->getMessage());
    }
}
public function getShopsForTransfer()
{
    try {
        Log::info('Getting shops for transfer');
        
        // Get all shops except Rabea
        $shops = Shop::where('name', '!=', 'rabea')
            // ->where('is_active', true)
            ->pluck('name');
            
        Log::info('Shops found', ['count' => $shops->count(), 'shops' => $shops->toArray()]);
            
        return response()->json([
            'success' => true,
            'shops' => $shops
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to fetch shops for transfer: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to load shops: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Process direct transfer from modal
 */
/**
 * Process direct transfer from modal
 */
public function processRabeaTransfer(Request $request)
{
    try {
        // Validate the request data
        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:gold_items,id',
            'shop_name' => 'required|string|exists:shops,name'
        ]);
        
        DB::beginTransaction();
        
        // Get destination shop and items
        $destinationShop = $validated['shop_name'];
        $items = GoldItem::whereIn('id', $validated['item_ids'])->get();
        
        Log::info('Processing Rabea transfer via modal', [
            'destination_shop' => $destinationShop,
            'item_count' => count($validated['item_ids']),
            'items' => $items->pluck('id')->toArray()
        ]);
        
        $processedCount = 0;
        $skippedItems = [];
        
        // Process each item
        foreach ($items as $item) {
            // Only skip items that are specifically in pending_transfer state
            // This allows items with other statuses to be transferred
            if ($item->status === 'pending_transfer') {
                Log::info('Skipping item with pending_transfer status', [
                    'item_id' => $item->id,
                    'serial' => $item->serial_number,
                    'current_status' => $item->status
                ]);
                $skippedItems[] = $item->serial_number;
                continue;
            }
            
            Log::info('Processing item for transfer', [
                'item_id' => $item->id,
                'serial' => $item->serial_number,
                'current_status' => $item->status
            ]);
            
            // Create transfer request record in transfer_requests table
            TransferRequest::create([
                'gold_item_id' => $item->id,
                'from_shop_name' => 'rabea',
                'to_shop_name' => $destinationShop,
                'status' => 'pending',
                'type' => 'item' // Add this if your table has a type field
            ]);
            
            // Update item status to pending_transfer
            $item->status = 'pending_transfer';
            $item->save();
            
            $processedCount++;
        }
        
        if ($processedCount === 0) {
            DB::rollBack();
            Log::warning('No items were processed for transfer', [
                'skipped_items' => $skippedItems
            ]);
            return response()->json([
                'success' => false,
                'message' => 'No items were processed. All selected items may already be in process.'
            ]);
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => "Transfer requests created successfully for {$processedCount} items"
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to process Rabea transfer: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to process transfer: ' . $e->getMessage()
        ], 422);
    }
}
} 