<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddRequest;
use App\Models\PoundRequest;
use App\Models\GoldItem;
use App\Models\Models;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Log;

class SuperAdminRequestController extends Controller
{
    public function index()
    {
        // Get all pending item requests
        $itemRequests = AddRequest::where('status', 'pending')->get();

        // Get all pending pound requests
        $poundRequests = PoundRequest::with('goldPound')
            ->where('status', 'pending')
            ->get();

        return view('superadmin.requests.index', compact('itemRequests', 'poundRequests'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action'); // 'accept' or 'reject'
        $selectedRequests = $request->input('selected_requests', []); 

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
                    $goldItemData['source'] = $itemRequest->source;

                    GoldItem::create($goldItemData);
                    $itemRequest->update(['status' => 'accepted']);
                } elseif ($action === 'reject') {
                    // Move to warehouse table
                    $warehouseData = $itemRequest->toArray();
                    unset($warehouseData['id']);
                    unset($warehouseData['shop_name']);
                    unset($warehouseData['shop_id']);
                    unset($warehouseData['rest_since']);

                    $warehouseData['status'] = 'rejected';

                    // Validate model exists
                    if (!Models::where('model', $warehouseData['model'])->exists()) {
                        continue;
                    }

                    // Check for duplicate serial number
                    if (Warehouse::where('serial_number', $warehouseData['serial_number'])->exists()) {
                        continue;
                    }

                    Warehouse::create($warehouseData);
                    $itemRequest->update(['status' => 'rejected']);
                }
            }

            $message = $action === 'accept' ? 'Items added successfully' : 'Requests rejected successfully.';
            return redirect()->route('superadmin.requests.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
} 