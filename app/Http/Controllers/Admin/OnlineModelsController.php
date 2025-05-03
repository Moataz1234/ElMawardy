<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OnlineModel;
use App\Models\Models;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class OnlineModelsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $onlineModels = OnlineModel::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.online_models.index', compact('onlineModels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.online_models.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:online_models,sku',
            'notes' => 'nullable|string'
        ]);

        // Check if SKU exists in Models table
        $modelExists = Models::where('SKU', $validated['sku'])->exists();
        if (!$modelExists) {
            return redirect()->back()->with('error', 'SKU does not exist in models database');
        }

        OnlineModel::create($validated);
        return redirect()->route('online-models.index')->with('success', 'Online model added successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        OnlineModel::findOrFail($id)->delete();
        return redirect()->route('online-models.index')->with('success', 'Online model removed successfully');
    }

    /**
     * Show form for Excel import.
     */
    public function showImportForm()
    {
        return view('admin.online_models.import');
    }

    /**
     * Import SKUs from Excel.
     */
    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!$request->hasFile('excel_file')) {
            return redirect()->back()->with('error', 'Please upload a file');
        }

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Assume first row is headers, so start from index 1
            $importedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;

            // Skip header row
            if (count($rows) > 1) {
                array_shift($rows);
            }

            foreach ($rows as $row) {
                // Assume the SKU is in the first column
                $sku = trim($row[0] ?? '');
                if (empty($sku)) {
                    $skippedCount++;
                    continue;
                }

                // Check if SKU exists in the Models table
                $modelExists = Models::where('SKU', $sku)->exists();
                if (!$modelExists) {
                    Log::warning('SKU not found in models database: ' . $sku);
                    $errorCount++;
                    continue;
                }

                // Check if SKU already exists in OnlineModel table
                $existingModel = OnlineModel::where('sku', $sku)->first();
                if ($existingModel) {
                    $skippedCount++;
                    continue;
                }

                try {
                    OnlineModel::create([
                        'sku' => $sku,
                        'notes' => $row[1] ?? null
                    ]);
                    $importedCount++;
                } catch (\Exception $e) {
                    Log::error('Error importing SKU: ' . $sku . ' - ' . $e->getMessage());
                    $errorCount++;
                }
            }

            $message = "Import completed: {$importedCount} added, {$skippedCount} skipped, {$errorCount} errors.";
            return redirect()->route('online-models.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Excel import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Clear all online models.
     */
    public function clearAll()
    {
        OnlineModel::truncate();
        return redirect()->route('online-models.index')->with('success', 'All online models have been cleared');
    }
}
