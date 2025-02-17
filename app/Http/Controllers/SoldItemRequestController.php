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

// ... other imports

class SoldItemRequestController extends Controller
{
    private SellService $saleService;
    public function __construct(
        SellService $saleService,
    ) {
        $this->saleService = $saleService;
    }

    public function viewSaleRequestsAcc(Request $request)
    {
        $query = SaleRequest::with(['goldItem', 'customer']);

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

        return view('Temp.Acc_sold_requests', compact('soldItemRequests'));
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
    public function approveSaleRequest($id)
    {
        $request = SaleRequest::findOrFail($id);
        $request->update([
            'status' => 'approved',
            'approver_shop_name' => Auth::user()->shop_name
        ]);
        $this->saleService->approveSale($request);
        return redirect()->back()->with('success', 'Sale request approved and item marked as sold');
    }


    public function rejectSaleRequest($id)
    {
        $request = SaleRequest::findOrFail($id);
        $request->update([
            'status' => 'rejected',
            'approver_shop_name' => Auth::user()->shop_name
        ]);
        return redirect()->back()->with('success', 'Sale request rejected');
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
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }
}
