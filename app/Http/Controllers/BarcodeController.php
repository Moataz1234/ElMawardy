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
        
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
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
            'H1' => 'Weight'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style the header row
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        // Get data filtered by shop
        $query = GoldItem::query();

        if ($request->filled('shop_id')) {
            Log::info('Filtering by shop_id: ' . $request->shop_id);
            $query->where('shop_id', $request->shop_id);
        }

        $goldItems = $query->get();
        Log::info('Number of filtered items: ' . $goldItems->count());

        // Add data rows - two items per row
        $row = 2;
        foreach ($goldItems->chunk(2) as $chunk) {
            // First item in the chunk
            if (isset($chunk[0])) {
                $sheet->setCellValue('A' . $row, $chunk[0]->serial_number);
                $sheet->setCellValue('B' . $row, $chunk[0]->shop_name ?? 'Admin');
                $sheet->setCellValue('C' . $row, $chunk[0]->model);
                $sheet->setCellValue('D' . $row, $chunk[0]->weight);
            }

            // Second item in the chunk (if exists)
            if (isset($chunk[1])) {
                $sheet->setCellValue('E' . $row, $chunk[1]->serial_number);
                $sheet->setCellValue('F' . $row, $chunk[1]->shop_name ?? 'Admin');
                $sheet->setCellValue('G' . $row, $chunk[1]->model);
                $sheet->setCellValue('H' . $row, $chunk[1]->weight);
            }

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer and prepare response
        $writer = new Xlsx($spreadsheet);
        
        // Generate filename with shop name if filtered
        $filename = 'barcode-list';
        if ($request->filled('shop_id')) {
            $shop = Shop::find($request->shop_id);
            if ($shop) {
                $filename .= '-' . Str::slug($shop->name);
            }
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