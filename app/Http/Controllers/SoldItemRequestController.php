<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemRequest; // Make sure you have this import
use App\Models\GoldItemSold;
use App\Models\GoldItem; // Import GoldItem
use App\Models\SoldItemRequest;
use App\Models\SaleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\SellService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\GoldPoundSold;
use App\Models\GoldPoundInventory;
use App\Models\GoldPound;

// ... other imports

class SoldItemRequestController extends Controller
{
    private SellService $sellService;
    public function __construct(
        SellService $sellService,
    ) {
        $this->sellService = $sellService;
    }

    public function viewSaleRequestsAcc(Request $request)
    {
        $query = SaleRequest::with(['goldItem', 'customer'])
            ->where(function ($q) {
                // Include all items (item_type != 'pound')
                $q->where('item_type', '!=', 'pound')
                  // Include standalone pounds (item_type = 'pound' AND related_item_serial is null)
                  ->orWhere(function ($q) {
                      $q->where('item_type', 'pound')
                        ->whereNull('related_item_serial');
                  });
            });
    
        // Apply date filter if provided
        if ($request->has('filter_date')) {
            $query->whereDate('created_at', $request->filter_date);
        }
    
        // Apply status filter, default to 'pending'
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }
    
        $soldItemRequests = $query->orderBy('created_at', 'desc')->get();
    
        // For items, load associated pound requests if they exist (for display or approval logic)
        $soldItemRequests = $soldItemRequests->map(function ($request) {
            if ($request->item_type !== 'pound') {
                $request->associatedPound = SaleRequest::where('related_item_serial', $request->item_serial_number)
                    ->where('item_type', 'pound')
                    ->first();
            }
            return $request;
        });
    
        return view('Accountant.Acc_sold_requests', compact('soldItemRequests'));
    }


    public function viewSaleRequests()
    {
        $soldItemRequests = SaleRequest::where('status', 'pending')->get();
        return view('admin.Requests.sold_requests', compact('soldItemRequests'));
    }
    public function viewAllSaleRequests()
    {
        $soldItemRequests = SaleRequest::all();
        return view('shops.all_sale_requests', compact('soldItemRequests'));
    }
    public function bulkApprove(Request $request)
    {
        try {
            Log::info('Bulk approve request received', ['all_data' => $request->all()]);

            return DB::transaction(function () use ($request) {
                $requests = $request->input('requests', []);
                
                Log::info('Starting transaction', ['request_count' => count($requests)]);

                foreach ($requests as $requestId) {
                    $saleRequest = SaleRequest::findOrFail($requestId);
                    Log::info('Processing request', [
                        'request_id' => $requestId,
                        'serial_number' => $saleRequest->item_serial_number,
                        'item_type' => $saleRequest->item_type
                    ]);

                    if ($saleRequest->item_type === 'pound') {
                        // Handle Pound Sale
                        $poundInventory = GoldPoundInventory::where('serial_number', $saleRequest->item_serial_number)->first();
                        
                        if ($poundInventory) {
                            // Create record in gold_pounds_sold
                            GoldPoundSold::create([
                                'serial_number' => $poundInventory->serial_number,
                                'gold_pound_id' => $poundInventory->gold_pound_id,
                                'shop_name' => $saleRequest->shop_name,
                                'price' => $saleRequest->price,
                                'customer_id' => $saleRequest->customer_id
                            ]);

                            // Delete from gold_pounds_inventory
                            $poundInventory->delete();
                            Log::info('Pound moved to sold table', ['serial_number' => $poundInventory->serial_number]);
                        }
                    } else {
                        // Handle Item Sale
                        $goldItem = GoldItem::where('serial_number', $saleRequest->item_serial_number)->first();

                        if ($goldItem) {
                            // First verify the model exists in the models table
                            $modelExists = DB::table('models')->where('model', $goldItem->model)->exists();
                            
                            if (!$modelExists) {
                                Log::error('Model not found in models table', [
                                    'model' => $goldItem->model,
                                    'item_serial' => $goldItem->serial_number
                                ]);
                                throw new \Exception("Invalid model reference: {$goldItem->model}");
                            }

                            // Create record in gold_items_sold
                            GoldItemSold::create([
                                'serial_number' => $goldItem->serial_number,
                                'shop_name' => $saleRequest->shop_name,
                                'shop_id' => $goldItem->shop_id,
                                'kind' => $goldItem->kind,
                                'model' => $goldItem->model,
                                'talab' => $goldItem->talab,
                                'gold_color' => $goldItem->gold_color,
                                'stones' => $goldItem->stones,
                                'metal_type' => $goldItem->metal_type,
                                'metal_purity' => $goldItem->metal_purity,
                                'quantity' => $goldItem->quantity,
                                'weight' => $goldItem->weight,
                                'add_date' => $goldItem->rest_since,
                                'price' => $saleRequest->price,
                                'sold_date' => now(),
                                'customer_id' => $saleRequest->customer_id
                            ]);

                            // Delete the original item
                            $goldItem->delete();
                            Log::info('Item moved to sold table', ['serial_number' => $goldItem->serial_number]);

                            // Check for associated pound sale request
                            $associatedPoundRequest = SaleRequest::where('related_item_serial', $saleRequest->item_serial_number)
                                ->where('item_type', 'pound')
                                ->first();

                            if ($associatedPoundRequest) {
                                Log::info('Found associated pound request', ['pound_request_id' => $associatedPoundRequest->id]);
                                
                                // Handle the pound sale
                                $poundInventory = GoldPoundInventory::where('serial_number', $associatedPoundRequest->item_serial_number)->first();
                                
                                if ($poundInventory) {
                                    // Create record in gold_pounds_sold
                                    GoldPoundSold::create([
                                        'serial_number' => $poundInventory->serial_number,
                                        'gold_pound_id' => $poundInventory->gold_pound_id,
                                        'shop_name' => $associatedPoundRequest->shop_name,
                                        'price' => $associatedPoundRequest->price,
                                        'customer_id' => $associatedPoundRequest->customer_id
                                    ]);

                                    // Delete from gold_pounds_inventory
                                    $poundInventory->delete();
                                    
                                    // Update pound request status
                                    $associatedPoundRequest->update([
                                        'status' => 'approved',
                                        'approver_shop_name' => Auth::user()->shop_name
                                    ]);
                                    
                                    Log::info('Associated pound moved to sold table', ['serial_number' => $poundInventory->serial_number]);
                                }
                            }
                        }
                    }

                    // Update request status
                    $saleRequest->update([
                        'status' => 'approved',
                        'approver_shop_name' => Auth::user()->shop_name
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Requests approved successfully'
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error in bulk approve', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving requests: ' . $e->getMessage()
            ], 500);
        }
    }
    // public function approveSaleRequest($id)
    // {
    //     DB::transaction(function () use ($id) {
    //         $request = SaleRequest::findOrFail($id);

    //         // Update request status
    //         $request->update([
    //             'status' => 'approved',
    //             'approver_shop_name' => Auth::user()->shop_name
    //         ]);

    //         // Check if this is a pound sale
    //         if ($request->item_type === 'pound') {
    //             // Get the pound from inventory
    //             $poundInventory = GoldPoundInventory::where('serial_number', $request->item_serial_number)
    //                 ->where('status', 'pending_sale')
    //                 ->first();

    //             if (!$poundInventory) {
    //                 throw new \Exception('Gold pound not found in inventory or not in pending status');
    //             }

    //             // Create record in gold_pounds_sold
    //             GoldPoundSold::create([
    //                 'serial_number' => $request->item_serial_number,
    //                 'gold_pound_id' => $poundInventory->gold_pound_id,
    //                 'shop_name' => $request->shop_name,
    //                 'price' => $request->price,
    //                 'customer_id' => $request->customer_id
    //             ]);

    //             // Remove from inventory after creating sold record
    //             $poundInventory->delete();
    //         } else {
    //             // Handle regular item sale
    //             $this->sellService->approveSale($request);
    //         }
    //     });

    //     return redirect()->back()->with('success', 'Sale request approved and item marked as sold');
    // }


    public function rejectSaleRequest($id)
    {
        DB::transaction(function () use ($id) {
            $request = SaleRequest::findOrFail($id);
            
            // Update request status
            $request->update([
                'status' => 'rejected',
                'approver_shop_name' => Auth::user()->shop_name
            ]);

            // Handle gold item rejection
            if ($request->item_type !== 'pound') {
                $goldItem = GoldItem::where('serial_number', $request->item_serial_number)->first();
                if ($goldItem) {
                    $goldItem->update(['status' => 'available']);
                }

                // Find and reject associated pound request if exists
                $poundRequest = SaleRequest::where('customer_id', $request->customer_id)
                    ->where('item_type', 'pound')
                    ->where('created_at', $request->created_at)
                    ->first();

                if ($poundRequest) {
                    $poundRequest->update([
                        'status' => 'rejected',
                        'approver_shop_name' => Auth::user()->shop_name
                    ]);

                    // Update pound inventory status
                    $poundInventory = GoldPoundInventory::where('serial_number', $poundRequest->item_serial_number)
                        ->first();
                    if ($poundInventory) {
                        $poundInventory->update(['status' => 'available']);
                    }
                }
            } else {
                // Handle pound rejection
                $poundInventory = GoldPoundInventory::where('serial_number', $request->item_serial_number)
                    ->first();
                if ($poundInventory) {
                    $poundInventory->update(['status' => 'available']);
                }
            }
        });

        return redirect()->back()->with('success', 'Sale request rejected and item status updated');
    }

    public function exportSales(Request $request)
    {
        $query = SaleRequest::with(['goldItem', 'customer']);

        // Apply date filter if provided
        if ($request->filled('filter_date')) {
            $query->whereDate('created_at', $request->filter_date);
        }

        // Apply status filter
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $sales = $query->get();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Serial Number',
            'Shop Name',
            'Weight',
            'Price',
            'Price/Gram',
            'Payment Method',
            'Status',
            'Customer Name',
            'Customer Phone',
            'Date'
        ];

        foreach (range('A', 'J') as $key => $column) {
            $sheet->setCellValue($column . '1', $headers[$key]);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getStyle($column . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('CCCCCC');
        }

        // Add data
        $row = 2;
        foreach ($sales as $sale) {
            $weight = $sale->item_type === 'pound' ? $sale->weight : ($sale->goldItem->weight ?? 0);
            $pricePerGram = $weight > 0 ? round($sale->price / $weight, 2) : 0;

            $sheet->setCellValue('A' . $row, $sale->item_serial_number);
            $sheet->setCellValue('B' . $row, $sale->shop_name);
            $sheet->setCellValue('C' . $row, $weight . 'g');
            $sheet->setCellValue('D' . $row, $sale->price . ' ' . config('app.currency'));
            $sheet->setCellValue('E' . $row, $pricePerGram . ' ' . config('app.currency') . '/g');
            $sheet->setCellValue('F' . $row, $sale->payment_method ?? 'N/A');
            $sheet->setCellValue('G' . $row, ucfirst($sale->status));
            $sheet->setCellValue('H' . $row, $sale->customer ? $sale->customer->first_name . ' ' . $sale->customer->last_name : 'N/A');
            $sheet->setCellValue('I' . $row, $sale->customer ? $sale->customer->phone_number : 'N/A');
            $sheet->setCellValue('J' . $row, $sale->created_at->format('Y-m-d H:i'));
            $row++;
        }

        // Add totals row
        $lastRow = $row - 1;
        $sheet->setCellValue('B' . $row, 'Totals:');
        $sheet->setCellValue('C' . $row, '=SUM(C2:C' . $lastRow . ')');
        $sheet->setCellValue('D' . $row, '=SUM(D2:D' . $lastRow . ')');
        $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);

        // Auto size columns
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer and save file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'sales_report_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }
}
