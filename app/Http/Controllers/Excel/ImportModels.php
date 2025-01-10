<?php

namespace App\Http\Controllers\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;

class ImportModels extends Controller
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
                        Models::create($record);
                        $rowsProcessed++;
                    } catch (QueryException $e) {
                        // Check if it's a duplicate entry error
                        if ($e->errorInfo[1] === 1062) {
                            $duplicatesSkipped++;
                            $this->skippedRows[] = [
                                'row' => $startRow + $index,
                                'model' => $record['model'],
                                'reason' => 'Duplicate model'
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
            $rowData = $sheet->rangeToArray('A' . $row . ':I' . $row, null, true, false)[0];

            // Skip empty rows
            if (empty(array_filter($rowData, function ($value) {
                return !is_null($value) && $value !== '';
            }))) {
                continue;
            }
            $first_production = $this->parseDate($rowData[6]);

            $records[] = [
                'model' => $rowData[0], // Column A in Excel
                'SKU' => $rowData[1], // Column B in Excel
                'scanned_image' => $rowData[2], // Column C in Excel
                'website_image' => $rowData[3], // Column D in Excel
                'stars' => $rowData[4], // Column E in Excel
                'source' => $rowData[5], // Column F in Excel
                'first_production' => $first_production, // Column G in Excel
                'semi_or_no' => $rowData[7], // Column H in Excel
                'average_of_stones' => $rowData[8], // Column I in Excel
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
        
        // if (strtotime($dateValue)) {
        //     return Carbon::parse($dateValue)->format('Y-m-d');
        // }

        // If the date is invalid or empty, return null
        return null;
    }
}
