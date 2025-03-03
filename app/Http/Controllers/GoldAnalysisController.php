<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\Shop;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;

class GoldAnalysisController extends Controller
{
    public function index()
    {
        // Get all shops except 'online'
        $shops = Shop::where('name', '!=', 'online')->get();

        // Get statistics for each shop
        $shopStatistics = [];
        foreach ($shops as $shop) {
            $statistics = GoldItem::select(
                'kind',
                DB::raw('COUNT(*) as total_items'),
                DB::raw('SUM(weight) as total_weight')
            )
            ->whereNotIn('status', ['sold', 'deleted'])
            ->where('shop_name', $shop->name)
            ->groupBy('kind')
            ->orderBy('kind')
            ->get();

            // Only store if shop has any items
            if ($statistics->isNotEmpty()) {
                $shopStatistics[$shop->id] = [
                    'shop' => $shop,
                    'statistics' => $statistics
                ];
            }
        }

        // Sort shops by ID
        ksort($shopStatistics);

        return view('gold-analysis.index', compact('shopStatistics'));
    }

    public function export()
    {
        $selectedShop = request('shop_name');

        // Get shop_id for the selected shop_name
        $shopId = null;
        if ($selectedShop) {
            $shopId = Shop::where('name', $selectedShop)->value('id');
        }

        $query = GoldItem::select(
            'kind',
            DB::raw('COUNT(*) as total_items'),
            DB::raw('SUM(weight) as total_weight')
        )
        ->whereNotIn('status', ['sold', 'deleted']);

        if ($selectedShop) {
            $query->where('shop_name', $selectedShop);
        }

        $statistics = $query->groupBy('kind')
            ->orderBy('kind')
            ->get();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Kind');
        $sheet->setCellValue('B1', 'Total Items');
        $sheet->setCellValue('C1', 'Total Weight (g)');

        // Style headers
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('CCCCCC');

        // Add data
        $row = 2;
        foreach ($statistics as $stat) {
            $sheet->setCellValue('A' . $row, $stat->kind);
            $sheet->setCellValue('B' . $row, $stat->total_items);
            // Set the weight as a number and format it
            $sheet->setCellValueExplicit('C' . $row, $stat->total_weight, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        // Add totals row
        $lastRow = $row;
        $sheet->setCellValue('A' . $lastRow, 'Total');
        $sheet->setCellValue('B' . $lastRow, '=SUM(B2:B' . ($lastRow-1) . ')');
        $sheet->setCellValue('C' . $lastRow, '=SUM(C2:C' . ($lastRow-1) . ')');
        
        // Format the totals row
        $sheet->getStyle('A'.$lastRow.':C'.$lastRow)->getFont()->setBold(true);
        $sheet->getStyle('C'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');

        // Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer and output file
        $writer = new Xlsx($spreadsheet);
        
        // Set dynamic filename based on selected shop
        $filename = $selectedShop && $shopId
            ? "جرد محل رقم {$shopId}.xlsx"
            : "جرد كل المحلات.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
} 