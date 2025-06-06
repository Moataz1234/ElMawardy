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
use App\Services\ExcelExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $query->whereDate('add_requests.rest_since', $date);

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
    public function update(Request $request, $id)
{
    try {
        $addRequest = AddRequest::findOrFail($id);

        // Validate the incoming data
        $validatedData = $request->validate([
            'serial_number' => 'sometimes|string|max:255',
            'model' => 'sometimes|string|max:255',
            'shop_name' => 'sometimes|string|max:255',
            'kind' => 'sometimes|string|max:255',
            'gold_color' => 'sometimes|string|max:255',
            'metal_type' => 'sometimes|string|max:255',
            'metal_purity' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|numeric|min:1',
            'weight' => 'sometimes|numeric|min:0',
            'talab' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:pending,accepted,rejected',
            'rest_since' => 'sometimes|date',
            'source' => 'sometimes|string|max:255'
        ]);

        // Update only the fields that are present in the request
        $addRequest->fill($validatedData);
        $addRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Add Request updated successfully',
            'data' => $addRequest
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update Add Request',
            'error' => $e->getMessage()
        ], 500);
    }
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
                    
                    // Ensure source is transferred from the request to the gold item
                    $goldItemData['source'] = $itemRequest->source;

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

    public function export(Request $request)
    {
        $query = AddRequest::query()
            ->leftJoin('models', 'add_requests.model', '=', 'models.model')
            ->select('add_requests.*', 'models.stars', 'models.average_of_stones');
    
        // Apply filters
        if ($status = $request->get('status')) {
            $query->where('add_requests.status', $status);
        }
    
        if ($request->has('shop_name') && $request->shop_name != '') {
            $query->where('add_requests.shop_name', $request->shop_name);
        }
    
        if ($date = $request->get('date')) {
            $query->whereDate('add_requests.rest_since', $date);
        }
    
        // Sorting
        if ($sort = $request->get('sort')) {
            $direction = $request->get('direction', 'desc');
            $query->orderBy('add_requests.created_at', $direction);
        }
    
        $requests = $query->with('modelCategory')->get();
    
        // Create and export excel
        $excelService = new ExcelExportService();
        $filename = $excelService->exportAddRequests($requests);
    
        // Return file download response
        return response()->download(
            storage_path('app/public/' . $filename),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }
    
    // New method for print functionality
    public function printRequests(Request $request)
    {
        $query = AddRequest::query();
    
        // Apply filters similar to export method
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
    
        if ($request->has('shop_name') && $request->shop_name != '') {
            $query->where('shop_name', $request->shop_name);
        }
    
        if ($date = $request->get('date')) {
            $query->whereDate('rest_since', $date);
        }
    
        // Sorting
        $query->orderBy('created_at', 'desc');
    
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
    
        return view('admin.requests.print_requests', compact('requests', 'shops'));
    }
}
