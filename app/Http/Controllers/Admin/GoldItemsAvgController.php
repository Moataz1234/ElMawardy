<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldItemsAvg;
use App\Models\Models;
use Illuminate\Http\Request;

class GoldItemsAvgController extends Controller
{
    // Display a listing of the resource
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Query the gold_items_avg table based on the search term
        $goldItemsAvg = GoldItemsAvg::when($search, function ($query, $search) {
                return $query->where('model', 'like', "%{$search}%")
                             ->orWhere('stones_weight', 'like', "%{$search}%");
            })
            ->paginate(10); // Adjust pagination as needed
    
        return view('admin.AVG_of_stones.index', compact('goldItemsAvg'));
    }

    // Show the form for creating a new resource
    public function create()
    {
        $models = Models::select('model')->get(); // Get all models

        return view('admin.AVG_of_stones.create',compact('models'));
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required|array', // Ensure model is an array
            'model.*' => 'required|string|exists:models,model', // Validate each model
            'stones_weight' => 'required|array', // Ensure stones_weight is an array
            'stones_weight.*' => 'required|numeric', // Validate each stones_weight
        ]);
    
        // Loop through the submitted data and create records
        foreach ($request->model as $index => $model) {
            GoldItemsAvg::create([
                'model' => $model,
                'stones_weight' => $request->stones_weight[$index],
            ]);
        }
    
        return redirect()->route('admin.gold_items_avg.index')->with('success', 'Records created successfully.');
    }

    // Show the form for editing the specified resource
    public function edit(GoldItemsAvg $goldItemsAvg)
    {
        return view('admin.AVG_of_stones.edit', compact('goldItemsAvg'));
    }

    // Update the specified resource in storage
    public function update(Request $request, GoldItemsAvg $goldItemsAvg)
    {
        $request->validate([
            'model' => 'required|string|exists:models,model',
            'stones_weight' => 'required|numeric',
        ]);

        $goldItemsAvg->update($request->all());

        return redirect()->route('admin.gold_items_avg.index')->with('success', 'Record updated successfully.');
    }

    // Remove the specified resource from storage
    public function destroy(GoldItemsAvg $goldItemsAvg)
    {
        $goldItemsAvg->delete();
        return redirect()->route('admin.gold_items_avg.index')->with('success', 'Record deleted successfully.');
    }
}