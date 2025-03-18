<?php

namespace App\Http\Controllers\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\GoldItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\QueryException;
use App\Models\Models;

class ImportGoldItems extends Controller
{
    protected $batchSize = 500;
    protected $timeLimit = 600; // 10 minutes
    protected $skippedRows = [];

    public function showForm()
    {
        return view('import');
    }

    public function import(Request $request)
    {
        // Set execution time limit
        ini_set('max_execution_time', $this->timeLimit);
        set_time_limit($this->timeLimit);
        
        // Validate request
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Load the spreadsheet
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            
            // Get total rows
            $highestRow = $sheet->getHighestRow();
            $totalBatches = ceil(($highestRow - 1) / $this->batchSize);
            $rowsProcessed = 0;
            $duplicatesSkipped = 0;
            
            // Process in batches
            for ($batch = 0; $batch < $totalBatches; $batch++) {
                $startRow = ($batch * $this->batchSize) + 2; // Start from row 2 to skip header
                $endRow = min($startRow + $this->batchSize - 1, $highestRow);
                
                $records = $this->processRowBatch($sheet, $startRow, $endRow);
                
                // Insert records one by one to handle duplicates
                foreach ($records as $index => $record) {
                    try {
                        GoldItem::create($record);
                        $rowsProcessed++;
                    } catch (QueryException $e) {
                        // Check if it's a duplicate entry error
                        if ($e->errorInfo[1] === 1062) {
                            $duplicatesSkipped++;
                            $this->skippedRows[] = [
                                'row' => $startRow + $index,
                                'serial_number' => $record['serial_number'],
                                'reason' => 'Duplicate serial number'
                            ];
                            continue; // Skip this record and continue with the next
                        }
                        // If it's not a duplicate error, rethrow it
                        throw $e;
                    }
                }
                
                // Free up memory
                unset($records);
                gc_collect_cycles();
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Prepare the response message
            $message = "Successfully imported {$rowsProcessed} records. ";
            if ($duplicatesSkipped > 0) {
                $message .= "Skipped {$duplicatesSkipped} duplicate entries.";
                // Store skipped rows in session for display
                session(['skipped_rows' => $this->skippedRows]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Log::error('Excel import failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
       protected function processRowBatch(Worksheet $sheet, int $startRow, int $endRow): array
    {
        $records = [];
        
        for ($row = $startRow; $row <= $endRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':O' . $row, null, true, false)[0];
            
            // Skip empty rows
            if (empty(array_filter($rowData))) {
                continue;
            }
            $rest_since = $this->parseDate($rowData[13]); // Column N in Excel

            // Get default source from Models table
            $defaultSource = Models::where('model', $rowData[5])->value('source');
            
            $records[] = [
                'model' => $rowData[5], //F
                'serial_number' => $rowData[1],//B
                'kind' => $rowData[4], //E
                'shop_name' => $rowData[2], //C
                'shop_id' => $rowData[3],//D
                'weight' => $rowData[12], //M
                'gold_color' => $rowData[7], //H
                'metal_type' => $rowData[9], //J
                'metal_purity' => $rowData[10], //K
                'quantity' => $rowData[11], //L
                'stones' => $rowData[8], //I
                'talab' => $rowData[6] === 'YES', // G
                'status' => 'available',
                'rest_since' => $rest_since,
                'source' => $rowData[14] ?: $defaultSource, // Column O in Excel, fallback to default source if empty
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        return $records;
    } 
    protected function parseDate($dateValue)
    {
        // If the date is a numeric Excel serial number, convert it
        if (is_numeric($dateValue)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
        }
    
        // If the date is already in a recognizable format, parse it
        if (strtotime($dateValue)) {
            return Carbon::parse($dateValue)->format('Y-m-d');
        }
    
        // If the date is invalid or empty, return null
        return null;
    }

    // Add new method to update sources for GoldItems
    public function updateSources(Request $request)
    {
        ini_set('max_execution_time', $this->timeLimit);
        set_time_limit($this->timeLimit);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            Log::info("Starting source update from file: " . $file->getClientOriginalName());

            $reader = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestDataRow();

            $updatedCount = 0;
            $notFoundCount = 0;
            $skippedCount = 0;

            // Process in batches
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':O' . $row, null, true, false)[0];
                
                // Skip if serial number or source is empty
                if (empty($rowData[1]) || empty($rowData[14])) { // Column B for serial number, O for source
                    $skippedCount++;
                    continue;
                }

                $serialNumber = trim($rowData[1]); // Column B
                $source = trim($rowData[14]); // Column O

                // Try to update in gold_items
                $goldItem = GoldItem::where('serial_number', $serialNumber)->first();
                if ($goldItem) {
                    $goldItem->update(['source' => $source]);
                    $updatedCount++;
                    Log::info("Updated source for gold item: {$serialNumber}");
                } else {
                    Log::info("Serial number not found: {$serialNumber}");
                    $notFoundCount++;
                }
            }

            $message = "Source update completed. Updated: {$updatedCount}, Not Found: {$notFoundCount}, Skipped: {$skippedCount}";
            Log::info($message);

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Source update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Source update failed: ' . $e->getMessage());
        }
    }
}
