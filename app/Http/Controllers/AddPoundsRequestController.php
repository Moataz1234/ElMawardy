<?php

namespace App\Http\Controllers;

use App\Models\PoundRequest;
use App\Models\GoldPoundInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AddPoundsRequestController extends Controller
{
    public function index()
    {
        $requests = PoundRequest::with('goldPound')
            ->where('shop_name', Auth::user()->shop_name)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.gold.pounds.requests.index', compact('requests'));
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'selected_requests' => 'required|array',
            'selected_requests.*' => 'exists:pound_requests,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $requests = PoundRequest::whereIn('id', $request->selected_requests)
                    ->where('shop_name', Auth::user()->shop_name)
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

                    if ($poundRequest->type === 'standalone') {
                        $inventory = GoldPoundInventory::where([
                            'gold_pound_id' => $poundRequest->gold_pound_id,
                            'shop_name' => $poundRequest->shop_name,
                            'type' => 'standalone',
                            'weight' => $weight,
                            'purity' => $purity
                        ])->first();

                        if ($inventory) {
                            $inventory->increment('quantity', 1);
                        } else {
                            GoldPoundInventory::create([
                                'gold_pound_id' => $poundRequest->gold_pound_id,
                                'shop_name' => $poundRequest->shop_name,
                                'type' => 'standalone',
                                'serial_number' => $poundRequest->serial_number,
                                'weight' => $weight,
                                'purity' => $purity,
                                'quantity' => 1
                            ]);
                        }
                    } else {
                        GoldPoundInventory::create([
                            'gold_pound_id' => $poundRequest->gold_pound_id,
                            'serial_number' => $poundRequest->serial_number,
                            'shop_name' => $poundRequest->shop_name,
                            'type' => 'in_item',
                            'weight' => $weight,
                            'purity' => $purity,
                            'quantity' => 1
                        ]);
                    }
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

    public function bulkReject(Request $request)
    {
        $request->validate([
            'selected_requests' => 'required|array',
            'selected_requests.*' => 'exists:add_pound_requests,id'
        ]);

        try {
            PoundRequest::whereIn('id', $request->selected_requests)
                ->where('shop_name', Auth::user()->shop_name)
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
