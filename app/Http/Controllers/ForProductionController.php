<?php

namespace App\Http\Controllers;

use App\Models\ForProduction;
use App\Models\Models;
use App\Models\GoldItemSold;
use App\Models\GoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ForProductionController extends Controller
{
    /**
     * Display a listing of the production orders.
     */
    public function index()
    {
        $productionOrders = ForProduction::orderBy('order_date', 'desc')->paginate(15);
        return view('admin.production.index', compact('productionOrders'));
    }

    /**
     * Show the form for creating a new production order.
     */
    public function create()
    {
        $models = Models::select('model')->distinct()->orderBy('model')->get();
        $goldColors = GoldItem::distinct()->pluck('gold_color')->filter()->sort()->values();
        return view('admin.production.create', compact('models', 'goldColors'));
    }

    /**
     * Store a newly created production order in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'gold_color' => 'required|string|max:255',
            'order_date' => 'required|date',
        ]);

        // Set not_finished equal to quantity initially
        $validatedData['not_finished'] = $validatedData['quantity'];

        try {
            ForProduction::create($validatedData);
            
            Log::info('Production order created', [
                'model' => $validatedData['model'],
                'quantity' => $validatedData['quantity'],
                'gold_color' => $validatedData['gold_color'],
                'order_date' => $validatedData['order_date']
            ]);

            return redirect()->route('production.index')
                ->with('success', 'Production order created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating production order', [
                'error' => $e->getMessage(),
                'data' => $validatedData
            ]);

            return redirect()->back()
                ->with('error', 'Error creating production order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified production order.
     */
    public function edit(ForProduction $production)
    {
        $models = Models::select('model')->distinct()->orderBy('model')->get();
        $goldColors = GoldItem::distinct()->pluck('gold_color')->filter()->sort()->values();
        return view('admin.production.edit', compact('production', 'models', 'goldColors'));
    }

    /**
     * Update the specified production order in storage.
     */
    public function update(Request $request, ForProduction $production)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'not_finished' => 'required|integer|min:0',
            'gold_color' => 'required|string|max:255',
            'order_date' => 'required|date',
        ]);

        try {
            $production->update($validatedData);
            
            Log::info('Production order updated', [
                'id' => $production->id,
                'model' => $validatedData['model'],
                'quantity' => $validatedData['quantity'],
                'not_finished' => $validatedData['not_finished'],
                'gold_color' => $validatedData['gold_color'],
                'order_date' => $validatedData['order_date']
            ]);

            return redirect()->route('production.index')
                ->with('success', 'Production order updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating production order', [
                'id' => $production->id,
                'error' => $e->getMessage(),
                'data' => $validatedData
            ]);

            return redirect()->back()
                ->with('error', 'Error updating production order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified production order from storage.
     */
    public function destroy(ForProduction $production)
    {
        try {
            $model = $production->model;
            $goldColor = $production->gold_color;
            $production->delete();
            
            Log::info('Production order deleted', [
                'id' => $production->id,
                'model' => $model,
                'gold_color' => $goldColor
            ]);

            return redirect()->route('production.index')
                ->with('success', 'Production order deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting production order', [
                'id' => $production->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error deleting production order: ' . $e->getMessage());
        }
    }

    /**
     * Get production status for a specific model and color combination (AJAX endpoint).
     */
    public function getModelStatus(Request $request)
    {
        $model = $request->query('model');
        $goldColor = $request->query('gold_color');
        
        $query = ForProduction::where('model', $model);
        
        if ($goldColor) {
            $query->where('gold_color', $goldColor);
        }
        
        $productionOrder = $query->first();
        
        if ($productionOrder) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'quantity' => $productionOrder->quantity,
                    'not_finished' => $productionOrder->not_finished,
                    'gold_color' => $productionOrder->gold_color,
                    'order_date' => $productionOrder->order_date->format('Y-m-d'),
                    'progress_percentage' => $productionOrder->quantity > 0 
                        ? round((($productionOrder->quantity - $productionOrder->not_finished) / $productionOrder->quantity) * 100, 2)
                        : 0
                ]
            ]);
        }
        
        return response()->json(['exists' => false]);
    }

    /**
     * Show the form for importing production data from Excel.
     */
    public function showImport()
    {
        return view('admin.production.import');
    }

    /**
     * Import production data from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row if it exists
            if (!empty($rows) && count($rows) > 1) {
                // Check if first row contains headers
                $firstRow = $rows[0];
                if (is_string($firstRow[0]) && strtolower($firstRow[0]) === 'model') {
                    array_shift($rows);
                }
            }

            $imported = 0;
            $errors = [];
            $defaultColorUsed = 0;

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $rowNumber = $index + 2; // Account for header and 0-based index

                try {
                    // Validate required fields
                    if (empty($row[0])) {
                        $errors[] = "Row {$rowNumber}: Model is required";
                        continue;
                    }

                    if (empty($row[1]) || !is_numeric($row[1]) || $row[1] <= 0) {
                        $errors[] = "Row {$rowNumber}: Valid quantity is required";
                        continue;
                    }

                    if (empty($row[2]) || !is_numeric($row[2]) || $row[2] < 0) {
                        $errors[] = "Row {$rowNumber}: Valid not_finished count is required";
                        continue;
                    }

                    $model = trim($row[0]);
                    $quantity = (int) $row[1];
                    $notFinished = (int) $row[2];
                    
                    // Handle gold_color - use Yellow as default if empty or null
                    $goldColor = 'Yellow'; // Default value
                    if (isset($row[3]) && !empty(trim($row[3]))) {
                        $goldColor = trim($row[3]);
                        
                        // Validate gold color (optional validation)
                        $validColors = ['Yellow', 'White', 'Rose'];
                        if (!in_array($goldColor, $validColors)) {
                            $errors[] = "Row {$rowNumber}: Invalid gold color '{$goldColor}'. Using 'Yellow' as default.";
                            $goldColor = 'Yellow';
                            $defaultColorUsed++;
                        }
                    } else {
                        $defaultColorUsed++;
                    }
                    
                    $orderDate = !empty($row[4]) ? $this->parseDate($row[4]) : now()->format('Y-m-d');

                    // Check if production order with same model and color already exists
                    $existingOrder = ForProduction::where('model', $model)
                        ->where('gold_color', $goldColor)
                        ->first();

                    if ($existingOrder) {
                        // Update existing order
                        $existingOrder->update([
                            'quantity' => $quantity,
                            'not_finished' => $notFinished,
                            'order_date' => $orderDate
                        ]);
                    } else {
                        // Create new order
                        ForProduction::create([
                            'model' => $model,
                            'quantity' => $quantity,
                            'not_finished' => $notFinished,
                            'gold_color' => $goldColor,
                            'order_date' => $orderDate
                        ]);
                    }

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: {$e->getMessage()}";
                }
            }

            if ($imported > 0) {
                $message = "{$imported} production orders imported successfully.";
                
                // Add info about default color usage
                if ($defaultColorUsed > 0) {
                    $message .= " {$defaultColorUsed} rows used default gold color (Yellow).";
                }
                
                if (!empty($errors)) {
                    return redirect()->route('production.index')
                        ->with('warning', $message . ' However, some rows had errors.')
                        ->with('import_errors', $errors);
                } else {
                    return redirect()->route('production.index')
                        ->with('success', $message);
                }
            } else {
                return redirect()->back()
                    ->with('error', 'No valid data found to import.')
                    ->with('import_errors', $errors);
            }

        } catch (\Exception $e) {
            Log::error('Production import error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);

            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Download sample template for production import.
     */
    public function downloadTemplate()
    {
        // Create sample data
        $sampleData = [
            ['Model', 'Quantity', 'Not Finished', 'Gold Color', 'Order Date'],
            ['5-1790', 100, 25, 'Yellow', '2024-01-15'],
            ['6-2100-A', 50, 10, 'White', '2024-01-16'],
            ['7-3500-B', 75, 0, 'Rose', '2024-01-17'],
            ['8-4000-C', 30, 15, '', '2024-01-18'], // Empty gold_color will default to Yellow
            ['9-5000-D', 60, 30, null, '2024-01-19'] // Null gold_color will default to Yellow
        ];

        // Create temporary file
        $filename = 'production_template_' . date('Y-m-d') . '.csv';
        $tempFile = tempnam(sys_get_temp_dir(), 'production_template');

        $handle = fopen($tempFile, 'w');
        foreach ($sampleData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($date)
    {
        if (is_numeric($date)) {
            // Excel date format
            $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
            return $excelDate->format('Y-m-d');
        }

        try {
            $carbonDate = \Carbon\Carbon::parse($date);
            return $carbonDate->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}

