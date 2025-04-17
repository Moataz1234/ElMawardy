<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemStatisticsController extends Controller
{
    public function index()
    {
        $shopName = Auth::user()->shop_name;
        
        // Get statistics only for the logged-in user's shop
        $statistics = GoldItem::select(
            'kind',
            DB::raw('COUNT(*) as total_items'),
            DB::raw('SUM(weight) as total_weight')
        )
        ->where('shop_name', $shopName)
        ->whereNotIn('status', ['sold', 'deleted'])
        ->groupBy('kind')
        ->orderBy('kind')
        ->get();

        return view('statistics.items_count', compact('statistics', 'shopName'));
    }
    
    public function export()
    {
        $shopName = Auth::user()->shop_name;
        
        // Get items data
        $items = GoldItem::where('shop_name', $shopName)
            ->whereNotIn('status', ['sold', 'deleted'])
            ->orderBy('model')
            ->get();
            
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'الرقم المسلسل');
        $sheet->setCellValue('B1', 'الموديل');
        $sheet->setCellValue('C1', 'الوزن');
        $sheet->setCellValue('D1', 'النوع');
        
        // Apply formatting to headers - make them bold
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        
        // Populate data rows
        $row = 2;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item->serial_number);
            $sheet->setCellValue('B' . $row, $item->model);
            $sheet->setCellValue('C' . $row, $item->weight);
            $sheet->setCellValue('D' . $row, $item->kind);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create response to download file
        $response = new StreamedResponse(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        
        // Set headers to download file
        $filename = $shopName . '_items.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
} 