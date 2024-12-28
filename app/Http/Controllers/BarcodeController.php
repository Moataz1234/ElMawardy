<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\Shop;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $query = GoldItem::query();
        
        // Filter by shop ID if provided
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }
    
        // Filter by a specific date or date range
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
    
        $goldItems = $query->get();
        $shops = Shop::all();
    
        return view('admin.Gold.Barcode', compact('goldItems', 'shops'));
    }
    
    public function export(Request $request)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Set headers for two items side by side (8 columns total)
        $headers = [
            'A1' => 'Serial Number',
            'B1' => 'Shop',
            'C1' => 'Model',
            'D1' => 'Weight',
            'E1' => 'Serial Number',
            'F1' => 'Shop',
            'G1' => 'Model',
            'H1' => 'Weight',
        ];
    
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
    
        // Style the header row
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    
        // Get data filtered by shop and date
        $query = GoldItem::query();
    
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }
    
        // Add date filtering logic
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
    
        $goldItems = $query->get();
    
        // Add data rows - two items per row
        $row = 2; // Start from the second row (first is for headers)
        $columnOffset = 0; // Keeps track of columns (0 for A-D, 4 for E-H)
        foreach ($goldItems as $index => $item) {
            if ($index % 2 == 0) {
                // Start a new row after every two items
                $columnOffset = 0;
            } else {
                // Offset for the second item in the same row
                $columnOffset = 4;
            }
    
            $sheet->setCellValue('A' . $row, $item->serial_number);
            $sheet->setCellValue('B' . $row, $item->shop_name ?? 'Admin');
            $sheet->setCellValue('C' . $row, $item->model);
            $sheet->setCellValue('D' . $row, $item->weight);
            
            // For the second item in the same row:
            $sheet->setCellValue('E' . $row, $item->serial_number);
            $sheet->setCellValue('F' . $row, $item->shop_name ?? 'Admin');
            $sheet->setCellValue('G' . $row, $item->model);
            $sheet->setCellValue('H' . $row, $item->weight);
    
            if ($index % 2 == 1) {
                // Increment row only after two items
                $row++;
            }
        }
    
        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        // Create writer and prepare response
        $writer = new Xlsx($spreadsheet);
    
        // Generate filename with shop name if filtered
        $filename = 'barcode';
        if ($request->filled('shop_id')) {
            $shop = Shop::find($request->shop_id);
            if ($shop) {
                $filename .= '-' . Str::slug($shop->name);
            }
        }
    
        // Add date filter to filename if provided
        if ($request->filled('date')) {
            $filename .= '-date-' . $request->date;
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $filename .= '-from-' . $request->start_date . '-to-' . $request->end_date;
        }
    
        $filename .= '-' . date('Y-m-d') . '.xlsx';
    
        // Buffer the output
        ob_start();
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_end_clean();
    
        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment;filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }
    
}    