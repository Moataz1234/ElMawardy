<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferRequestHistory;
use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TransferRequestsController extends Controller
{
    public function viewTransferRequestHistory(Request $request)
    {
        try {
            // Default values
            $status = $request->input('status', 'pending');
            $date = $request->input('date');
            $from_shop = $request->input('from_shop');
            $to_shop = $request->input('to_shop');
            $search = $request->input('search');

            Log::info('Transfer request history params:', [
                'status' => $status,
                'date' => $date,
                'from_shop' => $from_shop,
                'to_shop' => $to_shop,
                'search' => $search
            ]);

            if ($status === 'completed') {
                try {
                    $query = TransferRequestHistory::query();
                    
                    Log::info('Building completed requests query');

                    if ($date) {
                        $query->whereDate('transfer_completed_at', $date);
                        Log::info('Added date filter', ['date' => $date]);
                    }
                    
                    if ($from_shop) {
                        $query->where('from_shop_name', $from_shop);
                        Log::info('Added from_shop filter', ['from_shop' => $from_shop]);
                    }
                    
                    if ($to_shop) {
                        $query->where('to_shop_name', $to_shop);
                        Log::info('Added to_shop filter', ['to_shop' => $to_shop]);
                    }
                    
                    if ($search) {
                        $query->where(function($q) use ($search) {
                            $q->where('serial_number', 'like', "%{$search}%")
                              ->orWhere('model', 'like', "%{$search}%");
                        });
                        Log::info('Added search filter', ['search' => $search]);
                    }

                    $transferRequests = $query->orderBy('created_at', 'desc')->get();
                    Log::info('Successfully retrieved completed requests', ['count' => $transferRequests->count()]);
                } catch (\Exception $e) {
                    Log::error('Error in completed requests query', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                try {
                    $query = TransferRequest::with(['goldItem']);
                    
                    Log::info('Building pending requests query');

                    if ($date) {
                        $query->whereDate('created_at', $date);
                    }
                    
                    if ($status) {
                        $query->where('status', $status);
                    }
                    
                    if ($from_shop) {
                        $query->where('from_shop_name', $from_shop);
                    }
                    
                    if ($to_shop) {
                        $query->where('to_shop_name', $to_shop);
                    }
                    
                    if ($search) {
                        $query->whereHas('goldItem', function($q) use ($search) {
                            $q->where('serial_number', 'like', "%{$search}%")
                             ->orWhere('model', 'like', "%{$search}%");
                        });
                    }

                    $transferRequests = $query->orderBy('created_at', 'desc')->get();
                    Log::info('Successfully retrieved pending requests', ['count' => $transferRequests->count()]);
                } catch (\Exception $e) {
                    Log::error('Error in pending requests query', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            try {
                $shops = User::whereNotNull('shop_name')
                    ->where('shop_name', '!=', '')
                    ->pluck('shop_name')
                    ->unique();
                
                Log::info('Retrieved shops', ['count' => $shops->count()]);
            } catch (\Exception $e) {
                Log::error('Error retrieving shops', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            if ($request->ajax()) {
                $view = view('admin.Requests.transfer_requests_table', 
                    compact('transferRequests', 'status')
                )->render();
                
                return response()->json(['html' => $view]);
            }

            return view('admin.Requests.transfer_requests', 
                compact('transferRequests', 'shops', 'status')
            );

        } catch (\Exception $e) {
            Log::error('Transfer request history error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'status' => $status ?? 'unknown',
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'An error occurred while processing your request: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'An error occurred while processing your request: ' . $e->getMessage());
        }
    }
}
