<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\YourModel;
use App\Models\GoldItem;

class ExcelImportController extends Controller
{
    // Display the upload form
    public function showForm()
    {
        return view('import');
    }

    // Handle the file upload and import
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
    
        ini_set('max_execution_time', 300); // Increase execution time
    
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
    
        $chunkSize = 100; // Number of rows to process at a time
        $chunks = array_chunk($rows, $chunkSize);
    
        foreach ($chunks as $chunkIndex => $chunk) {
            // Skip the header row on the first chunk
            if ($chunkIndex === 0) {
                array_shift($chunk);
            }
    
            foreach ($chunk as $row) {
                GoldItem::create([
                    'link'              => $row[0] ?? null,
                    'serial_number'     => $row[1] ?? null,
                    'shop_name'         => $row[2] ?? null,
                    'shop_id'           => $row[3] ?? null,
                    'kind'              => $row[4] ?? null,
                    'model'             => $row[5] ?? null,
                    'talab'             => ($row[6] === 'YES') ? 1 : 0, // Convert to 1 or 0 for boolean
                    'gold_color'        => $row[7] ?? null,
                    'stones'            => $row[8] ?? null,
                    'metal_type'        => $row[9] ?? null,
                    'metal_purity'      => $row[10] ?? null,
                    'quantity'          => $row[11] ?? null,
                    'weight'            => $row[12] ?? null,
                    'rest_since'        => ($row[13] === 'OLD') ? null : $row[13], // Convert 'OLD' to null
                    'source'            => $row[14] ?? null,
                    'to_print'          => ($row[15] === ' ') ? null : $row[15],
                    'price'             => is_numeric($row[16]) ? (float) $row[16] : null, // Ensure it's numeric
                    'semi_or_no'        => $row[17] ?? null,
                    'average_of_stones' => ($row[18]==='Not Available') ?  null: $row[18],
                    'net_weight'        => $row[19] ?? null,
                    'website'           => $row[20] ??null, // Default value if not provided
                ]);
            }
        }
    
        return redirect()->back()->with('success', 'Data imported successfully!');
    }
}
