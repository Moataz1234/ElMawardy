<?php

namespace App\Http\Controllers;

use App\Models\ForProduction;
use App\Models\Models;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        return view('admin.production.create', compact('models'));
    }

    /**
     * Store a newly created production order in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'model' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'order_date' => 'required|date',
        ]);

        // Set not_finished equal to quantity initially
        $validatedData['not_finished'] = $validatedData['quantity'];

        try {
            ForProduction::create($validatedData);
            
            Log::info('Production order created', [
                'model' => $validatedData['model'],
                'quantity' => $validatedData['quantity'],
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
        return view('admin.production.edit', compact('production', 'models'));
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
            'order_date' => 'required|date',
        ]);

        try {
            $production->update($validatedData);
            
            Log::info('Production order updated', [
                'id' => $production->id,
                'model' => $validatedData['model'],
                'quantity' => $validatedData['quantity'],
                'not_finished' => $validatedData['not_finished'],
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
            $production->delete();
            
            Log::info('Production order deleted', [
                'id' => $production->id,
                'model' => $model
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
     * Get production status for a specific model (AJAX endpoint).
     */
    public function getModelStatus(Request $request)
    {
        $model = $request->query('model');
        
        $productionOrder = ForProduction::where('model', $model)->first();
        
        if ($productionOrder) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'quantity' => $productionOrder->quantity,
                    'not_finished' => $productionOrder->not_finished,
                    'order_date' => $productionOrder->order_date->format('Y-m-d'),
                    'progress_percentage' => $productionOrder->quantity > 0 
                        ? round((($productionOrder->quantity - $productionOrder->not_finished) / $productionOrder->quantity) * 100, 2)
                        : 0
                ]
            ]);
        }
        
        return response()->json(['exists' => false]);
    }
}
