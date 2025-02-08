<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldItem;
use App\Models\Shop;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            $query->whereDate('rest_since', $request->date);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('rest_since', [$request->start_date, $request->end_date]);
        } else {
            // Default to today's date if no other filters are applied
            $query->whereDate('rest_since', Carbon::today());
        }

        

        $goldItems = $query->get();

        // foreach ($goldItems as $item) {
        //     $item->modified_source =($item->modelCategory)->source;
        // }
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
            'E1' => 'to_print', // New header
            'F1' => 'Stars',  // New header
            'G1' => 'Serial Number',
            'H1' => 'Shop',
            'I1' => 'Model',
            'J1' => 'Weight',
            'K1' => 'to_print', // New header
            'L1' => 'Stars',  // New header
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style the header row
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        // Get data filtered by shop and date
        $query = GoldItem::query();

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        // Add date filtering logic
        if ($request->filled('date')) {
            $query->whereDate('rest_since', $request->date);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('rest_since', [$request->start_date, $request->end_date]);
        }

        $goldItems = $query->orderBy('shop_id')->get()->groupBy('shop_id');

        // Add data rows - two different items per row
        $row = 2; // Start from the second row (first is for headers)
        foreach ($goldItems as $shopId => $items) {
            foreach ($items as $i => $item) {
                // Set values for the first item in the row
                if ($i % 2 == 0) {
                    // New row for every first item in a pair or single item
                    $sheet->setCellValue('A' . $row, $item->serial_number);
                    $sheet->setCellValue('B' . $row, $item->shop_id ?? 'Admin');
                    $sheet->setCellValue('C' . $row, $item->model);
                    $sheet->setCellValue('D' . $row, $item->weight);

                    // New fields for first item
                    $source = optional($item->modelCategory)->source;
                    $sheet->setCellValue('E' . $row, $this->modifySource($source));
                    // $sheet->setCellValue('E' . $row, optional($item->modelCategory)->source);

                    $sheet->setCellValue('F' . $row, optional($item->modelCategory)->stars);
                }

                if (isset($items[$i + 1])) {
                    // Set values for the second item if it exists
                    $item2 = $items[$i + 1];
                    if ($i % 2 == 0) {
                        // Fill only when it's a new row for two items
                        $sheet->setCellValue('G' . $row, $item2->serial_number);
                        $sheet->setCellValue('H' . $row, $item2->shop_id ?? 'Admin');
                        $sheet->setCellValue('I' . $row, $item2->model);
                        $sheet->setCellValue('J' . $row, $item2->weight);

                        // New fields for second item
                        $source2 = optional($item2->modelCategory)->source;
                        $sheet->setCellValue('K' . $row, $this->modifySource($source2));
                        // $sheet->setCellValue('K' . $row, optional($item2->modelCategory)->source);
                        $sheet->setCellValue('L' . $row, optional($item2->modelCategory)->stars);
                    }
                } else {
                    // If no second item exists, leave cells empty or handle as needed
                    if ($i % 2 == 0) {
                        // Only fill empty cells when it's a new row for one item
                        for ($col = 'G'; $col <= 'L'; ++$col) {
                            $sheet->setCellValue($col . $row, '');
                        }
                    }
                }

                // Move to the next row after processing two items or one item alone
                if ($i % 2 == 1 || !isset($items[$i + 1])) {
                    // Increment row only after processing two items or a single item alone
                    ++$row;
                }
            }
        }

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
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
    private function modifySource($source)
    {
        if ($source === 'Production') {
            return '';
        }
        else{
        return strtoupper(substr($source, 0, 1));
        }
    }
}
