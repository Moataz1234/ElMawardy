<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddRequest;
use App\Models\GoldItem;
use App\Models\Models;

use App\Models\Shop;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
// use chillerlan\QRCode\QRCode; // Add QRCode library
// use chillerlan\QRCode\QROptions; // Add QROptions for configuration
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use chillerlan\QRCode\QRCode as chillerQRCode;
use chillerlan\QRCode\QROptions as chillerQROptions;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;


class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $query = AddRequest::query();

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

        foreach ($goldItems as $item) {
            $item->modified_source = $this->modifySource(optional($item)->source);
        }
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
            'E1' => 'To-Print',
            'F1' => 'Stars',
            'G1' => 'Serial Number',
            'H1' => 'Shop',
            'I1' => 'Model',
            'J1' => 'Weight',
            'K1' => 'To-Print',
            'L1' => 'Stars',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style the header row
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        // Get data filtered by shop and date
        $query = AddRequest::query();

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
                    $sheet->setCellValue('A' . $row, $item->serial_number);
                    $sheet->setCellValue('B' . $row, $item->shop_id ?? 'Admin');
                    $sheet->setCellValue('C' . $row, $item->model);
                    $sheet->setCellValue('D' . $row, $item->weight);

                    $source = optional($item)->source;
                    $sheet->setCellValue('E' . $row, $this->modifySource($source));
                    $sheet->setCellValue('F' . $row, optional($item->modelCategory)->stars);
                }

                if (isset($items[$i + 1])) {
                    $item2 = $items[$i + 1];
                    if ($i % 2 == 0) {
                        $sheet->setCellValue('G' . $row, $item2->serial_number);
                        $sheet->setCellValue('H' . $row, $item2->shop_id ?? 'Admin');
                        $sheet->setCellValue('I' . $row, $item2->model);
                        $sheet->setCellValue('J' . $row, $item2->weight);

                        $source2 = optional($item2)->source;
                        $sheet->setCellValue('K' . $row, $this->modifySource($source2));
                        $sheet->setCellValue('L' . $row, optional($item2->modelCategory)->stars);
                    }
                } else {
                    if ($i % 2 == 0) {
                        for ($col = 'G'; $col <= 'L'; ++$col) {
                            $sheet->setCellValue($col . $row, '');
                        }
                    }
                }

                if ($i % 2 == 1 || !isset($items[$i + 1])) {
                    ++$row;
                }
            }
        }

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer and prepare response
        $writer = new Xls($spreadsheet); // Changed from Xlsx to Xls

        // Set fixed filename
        $filename = 'System barcode.xls'; // Changed extension to .xls

        // Buffer the output
        ob_start();
        $writer->save('php://output');
        $content = ob_get_contents();
        ob_end_clean();

        return response($content)
            ->header('Content-Type', 'application/vnd.ms-excel') // Changed MIME type for .xls
            ->header('Content-Disposition', 'attachment;filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }
    // public function generate()
    // {
    //     $qr =  QrCode::format('png')
    //     ->size(300)
    //     ->generate('tel:+1234567890');
    //     return response($qr)->header('Content-Type', 'image/png');
    // }
    public function exportBarcode(Request $request)
    {
        $query = AddRequest::query();

        // Apply filters...
        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('rest_since', $request->date);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('rest_since', [$request->start_date, $request->end_date]);
        } else {
            $query->whereDate('rest_since', Carbon::today());
        }

        $goldItems = $query->with('modelCategory')->orderBy('shop_id')->get();

        if ($goldItems->isEmpty()) {
            return redirect()->back()->with('error', 'No items found for the selected filters.');
        }

        // Generate QR codes and prepare data
        $barcodeData = [];

        foreach ($goldItems as $item) {
            try {
                // Use QR Server API instead of Google Charts
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($item->serial_number);
                
                // Get shop ID
                $shopId = $item->shop_id ?? 'Admin';
                
                // Get stars and source from model
                $stars = '';
                $modelSource = '';
                if ($item->modelCategory) {
                    $stars = $item->modelCategory->stars;
                    $modelSource = $item->modelCategory->source;
                }
                
                // Add to data array
                $barcodeData[] = [
                    'serial_number' => $item->serial_number,
                    'model' => $item->model,
                    'weight' => $item->weight,
                    'shop_id' => $shopId,
                    'stars' => $stars,
                    'barcode_image' => $qrCodeUrl,
                    'source' => $this->modifySource($modelSource), // Use model's source instead of item's source
                ];
                
                Log::info('Generated QR code URL for: ' . $item->serial_number);
            } catch (\Exception $e) {
                Log::error('QR Code Error: ' . $e->getMessage() . ' for ' . $item->serial_number);
                continue;
            }
        }

        // Return direct HTML view for printing
        return view('admin.Gold.barcode_print', [
            'barcodeData' => $barcodeData
        ]);
    }
    private function modifySource($source)
    {
        if (!$source) return '';
        
        $sourceMap = [
            'Turkish' => 'T',
            'France' => 'F',
            'Market' => 'M',
            'Italy' => 'I'
        ];

        foreach ($sourceMap as $word => $letter) {
            if (stripos($source, $word) !== false) {
                return $letter;
            }
        }

        return '';
    }
    public function exportSingleItemBarcode($id)
    {
        try {
            // Find the specific gold item with its related model
            $item = GoldItem::with('modelCategory')->findOrFail($id);
    
            // Fetch stars from the related Models table
            $modelInfo = Models::where('model', $item->model)->first();
            $stars = $modelInfo ? $modelInfo->stars : 'N/A';
    
            // Generate QR code URL
            $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($item->serial_number);
            
            // Prepare barcode data for a single item
            $barcodeData = [
                'serial_number' => $item->serial_number,
                'model' => $item->model,
                'weight' => $item->weight,
                'shop_id' => $item->shop_id,
                'stars' => $stars,
                'barcode_image' => $qrCodeUrl,
                'source' => $this->modifySource($item->source),
            ];
    
            // Return the barcode print view for this single item
            return view('admin.Gold.barcode_print', [
                'barcodeData' => [$barcodeData]
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Barcode generation error for item ' . $id . ': ' . $e->getMessage());
            
            // Redirect back with an error message
            return redirect()->back()->with('error', 'Failed to generate barcode: ' . $e->getMessage());
        }
    }
}
