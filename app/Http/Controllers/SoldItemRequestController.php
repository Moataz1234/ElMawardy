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
use App\Models\Models;
use App\Models\TransferRequestHistory;

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
                            // Get model details for the additional fields
                            $modelDetails = Models::where('model', $goldItem->model)->first();

                            // Archive transfer requests before deleting them
                            foreach ($goldItem->transferRequests as $transferRequest) {
                                TransferRequestHistory::create([
                                    'from_shop_name' => $transferRequest->from_shop_name,
                                    'to_shop_name' => $transferRequest->to_shop_name,
                                    'status' => $transferRequest->status,
                                    'serial_number' => $goldItem->serial_number,
                                    'model' => $goldItem->model,
                                    'kind' => $goldItem->kind,
                                    'weight' => $goldItem->weight,
                                    'gold_color' => $goldItem->gold_color,
                                    'metal_type' => $goldItem->metal_type,
                                    'metal_purity' => $goldItem->metal_purity,
                                    'quantity' => $goldItem->quantity,
                                    'stones' => $goldItem->stones,
                                    'talab' => $goldItem->talab,
                                    'transfer_completed_at' => $transferRequest->updated_at,
                                    'item_sold_at' => now(),
                                    // Add new fields from Models table
                                    'stars' => $modelDetails ? $modelDetails->stars : null,
                                    'scanned_image' => $modelDetails ? $modelDetails->scanned_image : null
                                ]);
                            }

                            // First verify the model exists in the models table
                            $modelCategory = Models::where('model', $goldItem->model)->first();
                            
                            if (!$modelCategory) {
                                Log::error('Model not found in models table', [
                                    'model' => $goldItem->model,
                                    'item_serial' => $goldItem->serial_number
                                ]);
                                throw new \Exception("Invalid model reference: {$goldItem->model}");
                            }

                            // Create record in gold_items_sold with stars
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
                                'customer_id' => $saleRequest->customer_id,
                                'stars' => $modelCategory->stars,
                                'source' => $goldItem->source
                            ]);

                            // Delete transfer requests and original item
                            $goldItem->transferRequests()->delete();
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

        // Apply date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Apply shop filter
        if ($request->filled('shop_name')) {
            $query->where('shop_name', $request->shop_name);
        }

        // Apply status filter
        $status = $request->get('status', 'approved');
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
            // Get weight based on item type
            if ($sale->item_type === 'pound') {
                $poundSold = GoldPoundSold::where('serial_number', $sale->item_serial_number)->first();
                $poundInventory = GoldPoundInventory::where('serial_number', $sale->item_serial_number)->first();
                $pound = GoldPound::find($poundInventory ? $poundInventory->gold_pound_id : ($poundSold ? $poundSold->gold_pound_id : null));
                $weight = $pound ? $pound->weight : 0;
            } else {
                $soldItem = GoldItemSold::where('serial_number', $sale->item_serial_number)->first();
                $weight = $soldItem ? $soldItem->weight : 0;
            }

            $pricePerGram = $weight > 0 ? round($sale->price / $weight, 2) : 0;

            // Format cells properly for numerical calculations
            $sheet->setCellValue('A' . $row, $sale->item_serial_number);
            $sheet->setCellValue('B' . $row, $sale->shop_name);
            $sheet->setCellValue('C' . $row, $weight); // Remove 'g' suffix from the cell value
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('0.00"g"'); // Format with 'g' suffix
            
            $sheet->setCellValue('D' . $row, $sale->price); // Store as number
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00'); // Format with thousands separator
            
            $sheet->setCellValue('E' . $row, $pricePerGram); // Store as number
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00" ' . config('app.currency') . '/g"');
            
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
        $sheet->setCellValue('C' . $row, '=SUM(C2:C' . $lastRow . ')'); // Simple SUM function now works
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('0.00"g"');
        
        $sheet->setCellValue('D' . $row, '=SUM(D2:D' . $lastRow . ')');
        $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        
        $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);

        // Auto size columns
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer
        $writer = new Xlsx($spreadsheet);
        
        // Generate file name based on filters
        $fileName = 'sales_report';
        
        if ($request->filled('shop_name')) {
            $fileName .= '_' . $request->shop_name;
        }
        
        if ($request->filled('from_date') || $request->filled('to_date')) {
            if ($request->filled('from_date')) {
                $fileName .= '_from_' . $request->from_date;
            }
            if ($request->filled('to_date')) {
                $fileName .= '_to_' . $request->to_date;
            }
        } else {
            // Only add today's date if no date filter is applied
            $fileName .= '_' . date('Y-m-d');
        }
        
        $fileName .= '.xlsx';
        
        // Clean the filename by removing any special characters
        $fileName = preg_replace('/[^A-Za-z0-9._-]/', '_', $fileName);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    public function viewAllSoldItems(Request $request)
    {
        $query = SaleRequest::with(['goldItem', 'customer']);

        // Apply date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Apply shop filter
        if ($request->filled('shop_name')) {
            $query->where('shop_name', $request->shop_name);
        }

        // Apply status filter, default to 'approved' if no status is specified
        $status = $request->get('status', 'approved');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Get unique shop names for the dropdown
        $shops = SaleRequest::distinct('shop_name')->pluck('shop_name');

        // Get paginated results with 50 items per page
        $soldItemRequests = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString(); // This preserves the filter parameters

        return view('Accountant.all_sold_requests', compact('soldItemRequests', 'shops'));
    }
}
