<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoldItem;
use App\Models\Models;
use App\Models\Warehouse;
use App\Models\AddRequest;
use App\Models\PoundRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AddRequestController extends Controller
{
    public function allRequests(Request $request)
    {
        $query = AddRequest::query()
            ->leftJoin('models', 'add_requests.model', '=', 'models.model')
            ->select('add_requests.*', 'models.stars');

        // Default to pending status if not specified
        $status = $request->get('status', 'pending');
        if ($status) {
            $query->where('add_requests.status', $status);
        }

        // Filter by shop_name if provided
        if ($request->has('shop_name') && $request->shop_name != '') {
            $query->where('add_requests.shop_name', $request->shop_name);
        }

        // Filter by date if provided, default to today
        $date = $request->get('date', date('Y-m-d'));
        $query->whereDate('add_requests.created_at', $date);

        // Handle sorting
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'desc');
        
        if ($sort === 'date') {
            $query->orderBy('add_requests.created_at', $direction);
        }

        $requests = $query->get();

        $shops = [
            'Mohandessin Shop',
            'Mall of Arabia',
            'Nasr City',
            'Zamalek',
            'Mall of Egypt',
            'El Guezira Shop',
            'Arkan',
            'District 5',
            'U Venues'
        ];

        return view('admin.requests.add_requests', compact('requests', 'shops'));
    }


    public function index()
    {
        // Get item requests
        $itemRequests = AddRequest::where('shop_name', Auth::user()->shop_name)
            ->where('status', 'pending')
            ->get();

        // Get pound requests
        $poundRequests = PoundRequest::with('goldPound')
            ->where('shop_name', Auth::user()->shop_name)
            ->where('status', 'pending')
            ->get();

        return view('shops.add_requests', compact('itemRequests', 'poundRequests'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action'); // 'accept' or 'reject'
        $selectedRequests = $request->input('selected_requests', []); // Array of selected request IDs

        if (empty($selectedRequests)) {
            return redirect()->back()->with('error', 'No requests selected.');
        }

        try {
            foreach ($selectedRequests as $id) {
                $itemRequest = AddRequest::findOrFail($id);

                if ($action === 'accept') {
                    // Move to gold_items table
                    $goldItemData = $itemRequest->toArray();
                    unset($goldItemData['id']);
                    $goldItemData['status'] = 'available';

                    GoldItem::create($goldItemData);

                    // Update request status
                    $itemRequest->update(['status' => 'accepted']);
                } elseif ($action === 'reject') {
                    // Log the warehouse data for debugging
                    Log::info('Warehouse Data:', $itemRequest->toArray());

                    // Move to warehouse table
                    $warehouseData = $itemRequest->toArray();
                    unset($warehouseData['id']);
                    unset($warehouseData['shop_name']);
                    unset($warehouseData['shop_id']);
                    unset($warehouseData['rest_since']); // Remove rest_since for warehouse

                    $warehouseData['status'] = 'rejected';

                    // Log the warehouse data before creation
                    Log::info('Creating Warehouse Entry:', $warehouseData);

                    // Check if the model exists
                    $modelExists = Models::where('model', $warehouseData['model'])->exists();
                    if (!$modelExists) {
                        Log::error('Model does not exist:', ['model' => $warehouseData['model']]);
                        continue; // Skip this request
                    }

                    // Check if the serial_number already exists in the warehouse table
                    $existingWarehouseEntry = Warehouse::where('serial_number', $warehouseData['serial_number'])->first();
                    if ($existingWarehouseEntry) {
                        Log::error('Serial number already exists in warehouse:', ['serial_number' => $warehouseData['serial_number']]);
                        continue; // Skip this request
                    }

                    // Create the warehouse entry
                    try {
                        Warehouse::create($warehouseData);
                        $itemRequest->update(['status' => 'rejected']);
                    } catch (\Exception $e) {
                        Log::error('Failed to create warehouse entry:', [
                            'error' => $e->getMessage(),
                            'data' => $warehouseData,
                        ]);
                        continue; // Skip this request
                    }

                    // Update request status
                    Log::info('Request Status Updated to Rejected:', ['id' => $itemRequest->id]);
                }
            }

            $message = $action === 'accept' ? 'تم اضافة القطع بنجاح' : 'Requests rejected successfully.';
            return redirect()->route('add-requests.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
