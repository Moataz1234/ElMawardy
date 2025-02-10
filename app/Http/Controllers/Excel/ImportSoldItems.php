<?php

namespace App\Http\Controllers\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use App\Models\GoldItemSold;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\QueryException;

class ImportSoldItems extends Controller
{
    protected $batchSize = 500; // Increased batch size
    protected $timeLimit = 12000; // Increased time limit to 60 minutes
    protected $skippedRows = [];

    public function showForm()
    {
        return view('import');
    }

    public function import(Request $request)
    {
        ini_set('max_execution_time', $this->timeLimit);
        set_time_limit($this->timeLimit);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            $file = $request->file('file');
            Log::info("Starting import of file: " . $file->getClientOriginalName());

            // Create reader without read filter initially
            $reader = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);

            // Load spreadsheet without filter first to check structure
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            // Get the actual last column and row
            $highestColumn = $sheet->getHighestDataColumn();
            $highestRow = $sheet->getHighestDataRow();

            Log::info("Actual worksheet details:", [
                'name' => $sheet->getTitle(),
                'highest_column' => $highestColumn,
                'highest_row' => $highestRow,
                'dimension' => $sheet->calculateWorksheetDimension()
            ]);

            // Read first few rows to debug
            for ($row = 1; $row <= min(5, $highestRow); $row++) {
                $rowData = $sheet->rangeToArray(
                    'A' . $row . ':' . $highestColumn . $row,
                    null,
                    true,
                    false
                )[0];
                Log::info("Row {$row} data:", $rowData);
            }

            // Now proceed with the chunked reading
            $totalBatches = ceil(($highestRow - 1) / $this->batchSize);
            $rowsProcessed = 0;
            $duplicatesSkipped = 0;

            Log::info("Starting import for {$highestRow} rows in {$totalBatches} batches.");

            // Process in batches
            for ($batch = 0; $batch < $totalBatches; $batch++) {
                $startRow = ($batch * $this->batchSize) + 2; // Start from row 2 to skip header
                $endRow = min($startRow + $this->batchSize - 1, $highestRow);

                Log::info("Processing batch {$batch}: rows {$startRow} to {$endRow}.");

                $records = $this->processRowBatch($sheet, $startRow, $endRow);

                // Insert records one by one to handle duplicates
                foreach ($records as $index => $record) {
                    try {
                        // Check if the serial number exists in the database
                        $existingRecord = GoldItemSold::where('serial_number', $record['serial_number'])->first();

                        if ($existingRecord) {
                            // Log the old weight before updating
                            $oldWeight = $existingRecord->weight;
                            $newWeight = $record['weight'];
                
                            // Update only the weight column
                            $existingRecord->update(['weight' => $newWeight]);
                
                            // Log the weight update
                            Log::info("Weight updated for serial_number: {$record['serial_number']}", [
                                'old_weight' => $oldWeight,
                                'new_weight' => $newWeight,
                                'row' => $startRow + $index,
                            ]);
                 $rowsProcessed++;
                        } else {
                            // Create a new record if the serial number does not exist
                            GoldItemSold::create($record);
                            $rowsProcessed++;
                        }
                    } catch (QueryException $e) {
                        // Check if it's a duplicate entry error
                        if (GoldItemSold::where('serial_number', $record['serial_number'])->exists()) {
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

            Log::info("Import completed: {$message}");

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
            // Get the row data and log it
            $rowData = $sheet->rangeToArray('A' . $row . ':P' . $row, null, true, false)[0];
            Log::info("Raw row {$row} data:", $rowData);

            // Get serial number cell (Column B)
            $serialCell = $sheet->getCell('A' . $row);
            Log::info("Serial number cell value: " . $serialCell->getValue());
            Log::info("Serial number cell type: " . $serialCell->getDataType());

            // Skip rows where serial_number (column B) is empty
            if (empty($rowData[0])) {
                Log::info("Skipping row {$row}: serial_number is empty. Raw value: " . var_export($rowData[0], true));
                continue;
            }

            // Process the row
            $records[] = [
                'serial_number' => trim($rowData[0]),
                'model' => trim($rowData[4]),
                'shop_name' => trim($rowData[1]),
                'shop_id' => trim($rowData[2]),
                'kind' => trim($rowData[3]),
                'weight' => (float)str_replace(',', '', $rowData[11]),
                'gold_color' => trim($rowData[6]),
                'metal_type' => trim($rowData[8]),
                'metal_purity' => trim($rowData[9]),
                'quantity' => (int)$rowData[10],
                'add_date' => $this->parseDate($rowData[12]),
                'price' => (float)str_replace(',', '', $rowData[14]),
                'sold_date' => $this->parseDate(dateValue: $rowData[15]),
                'stones' => !empty($rowData[7]) ? $rowData[7] : null,
                'talab' => strtoupper(trim($rowData[5])) === 'YES',
                'stars' =>trim($rowData[13]),
                'customer_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            Log::info("Successfully processed row {$row} with serial number: {$rowData[0]}");
        }

        return $records;
    }
    protected function parseDate($dateValue)
    {
        if (empty($dateValue) || $dateValue === 'OLD') {
            return null;
        }

        // Remove any extra spaces
        $dateValue = trim($dateValue);

        // If the date is in dd/mm/yyyy format
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateValue)) {
            return Carbon::createFromFormat('d/m/Y', $dateValue)->format('Y-m-d');
        }

        // If the date is a numeric Excel serial number
        if (is_numeric($dateValue)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
        }

        // If the date is in any other recognizable format
        try {
            return Carbon::parse($dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Failed to parse date: {$dateValue}");
            return null;
        }
    }
}

// Custom read filter for chunk reading
class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;
    private $endRow = 0;

    public function setRows($startRow, $endRow)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        // If no range set, read all
        if ($this->startRow == 0 || $this->endRow == 0) {
            return true;
        }

        // Read the row if within range
        if ($row >= $this->startRow && $row <= $this->endRow) {
            return true;
        }
        return false;
    }
}
