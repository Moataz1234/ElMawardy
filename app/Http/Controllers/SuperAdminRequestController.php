<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddRequest;
use App\Models\PoundRequest;
use App\Models\GoldItem;
use App\Models\Models;
use App\Models\Warehouse;
use App\Models\GoldPoundInventory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        return view('super_Admin.Requests.Add_requests.index', compact('itemRequests', 'poundRequests'));
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

    public function bulkApprovePounds(Request $request)
    {
        $request->validate([
            'selected_requests' => 'required|array',
            'selected_requests.*' => 'exists:add_pound_requests,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $requests = PoundRequest::whereIn('id', $request->selected_requests)
                    ->where('status', 'pending')
                    ->get();

                foreach ($requests as $poundRequest) {
                    $poundRequest->update(['status' => 'approved']);

                    $weight = in_array($poundRequest->goldPound->kind, ['pound_varient', 'bar_varient']) || $poundRequest->custom_weight
                        ? $poundRequest->custom_weight
                        : $poundRequest->weight;

                    $purity = in_array($poundRequest->goldPound->kind, ['pound_varient', 'bar_varient']) || $poundRequest->custom_purity
                        ? $poundRequest->custom_purity
                        : $poundRequest->goldPound->purity;

                    // Create a new inventory entry for each request
                    GoldPoundInventory::create([
                        'gold_pound_id' => $poundRequest->gold_pound_id,
                        'shop_name' => $poundRequest->shop_name,
                        'type' => $poundRequest->type,
                        'serial_number' => $poundRequest->serial_number,
                        'weight' => $weight,
                        'purity' => $purity,
                        'quantity' => 1
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Requests approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving requests: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkRejectPounds(Request $request)
    {
        $request->validate([
            'selected_requests' => 'required|array',
            'selected_requests.*' => 'exists:add_pound_requests,id'
        ]);

        try {
            PoundRequest::whereIn('id', $request->selected_requests)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => 'تم رفض الطلبات المحددة'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفض الطلبات: ' . $e->getMessage()
            ], 500);
        }
    }
} 