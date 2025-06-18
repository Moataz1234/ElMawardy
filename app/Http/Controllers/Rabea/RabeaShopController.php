<?php

namespace App\Http\Controllers\Rabea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoldItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RabeaShopController extends Controller
{
    public function showWorkshopRequests()
    {
        // Check if the user's shop name is 'rabea'
        if (Auth::user()->shop_name !== 'rabea') {
            return redirect()->route('dashboard')
                ->with('error', 'Only Rabea shop can view workshop requests');
        }
        
        $query = DB::table('workshop_transfer_requests')
            ->where('shop_name', 'rabea');
            
        // Apply filters
        $request = request();
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
            ->paginate(20);
        
        return view('shops.workshop_requests', compact('requests'));
    }
    
    public function handleWorkshopRequests(Request $request)
    {
        // Check if the user's shop name is 'rabea'
        if (Auth::user()->shop_name !== 'rabea') {
            return redirect()->route('dashboard')
                ->with('error', 'Only Rabea shop can handle workshop requests');
        }
        
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
                    ->where('shop_name', 'rabea') // Ensure the request belongs to rabea shop
                    ->first();
                
                if (!$transferRequest) {
                    continue; // Skip if request not found or not belonging to rabea
                }
                
                // Get the gold item
                $goldItem = GoldItem::find($transferRequest->item_id);
                if (!$goldItem) {
                    continue; // Skip if gold item not found
                }
                
                if ($action === 'approve') {
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
                    
                    // Update status and delete the item
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
            }
            
            DB::commit();
            
            $message = ($action === 'approve') 
                ? 'Selected items transferred to workshop successfully' 
                : 'Selected workshop requests rejected successfully';
                
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle workshop requests: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function showRabeaItems(Request $request)
    {
        // Get all the gold items that are available in inventory
        $goldItems = GoldItem::with('modelCategory')
            ->where('shop_name', 'rabea')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('Rabea.items_list', compact('goldItems'));
    }
} 