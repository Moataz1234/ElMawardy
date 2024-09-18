<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\diamond; // Use your model to insert data

class NewItemController extends Controller
{
    // Display the form
    public function create()
    {
        return view('admin.new-item');
    }

    // Handle form submission
    public function store(Request $request)
    {
        $lastItem = diamond::orderBy('id', 'desc')->first();
    
        // Determine the new code (incrementing by 1)
        $newCode = $lastItem ? $lastItem->code + 1 : 1;
        
        // Validate the data, including new fields
        $validatedData = $request->validate([
            'code' => 'nullable|string|max:50',
            'kind' => 'required|string|max:100',
            'cost' => 'nullable|numeric',
            'calico1' => 'nullable|string|max:100',
            'weight1' => 'nullable|numeric',
            'calico2' => 'nullable|string|max:100',
            'number2' => 'nullable|integer',
            'weight2' => 'nullable|numeric',
            'calico3' => 'nullable|string|max:100',
            'number3' => 'nullable|integer',
            'weight3' => 'nullable|numeric',
            'calico4' => 'nullable|string|max:100',
            'number4' => 'nullable|integer',
            'weight4' => 'nullable|numeric',
            'calico5' => 'nullable|string|max:100',
            'number5' => 'nullable|integer',
            'weight5' => 'nullable|numeric',
            'calico6' => 'nullable|string|max:100',
            'number6' => 'nullable|integer',
            'weight6' => 'nullable|numeric',
            'calico7' => 'nullable|string|max:100',
            'number7' => 'nullable|integer',
            'weight7' => 'nullable|numeric',
            'calico8' => 'nullable|string|max:100',
            'number8' => 'nullable|integer',
            'weight8' => 'nullable|numeric',
            'calico9' => 'nullable|string|max:100',
            'number9' => 'nullable|integer',
            'weight9' => 'nullable|numeric',
            'calico10' => 'nullable|string|max:100',
            'number10' => 'nullable|integer',
            'weight10' => 'nullable|numeric',
            'calico11' => 'nullable|string|max:100',
            'number11' => 'nullable|integer',
            'weight11' => 'nullable|numeric',
            'calico12' => 'nullable|string|max:100',
            'number12' => 'nullable|integer',
            'weight12' => 'nullable|numeric',
            'cost1' => 'nullable|numeric',
            'cost2' => 'nullable|numeric',
            'cost3' => 'nullable|numeric',
            'cost4' => 'nullable|numeric',
            'cost5' => 'nullable|numeric',
            'cost6' => 'nullable|numeric',
            'cost7' => 'nullable|numeric',
            'cost8' => 'nullable|numeric',
            'cost9' => 'nullable|numeric',
            'cost10' => 'nullable|numeric',
            'cost11' => 'nullable|numeric',
            'cost12' => 'nullable|numeric',
            'image_path' => 'nullable|string|max:255',
            'certificate_code' => 'nullable|string|max:100',
            'daftar_number' => 'nullable|string|max:100',
            'sta' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'workshop' => 'nullable|string|max:100',
            'tarkeeb' => 'nullable|string|max:100',
            'gela' => 'nullable|numeric',
            'banue' => 'nullable|numeric',
            'date' => 'nullable|date',
            'condition' => 'nullable|string|max:50',
            'selling_date' => 'nullable|date',
            'selling_price' => 'nullable|numeric',
            'shop' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
            'return' => 'nullable|string|max:50',
            'date_r' => 'nullable|date',
            'details' => 'nullable|string',
        ]);

        // Add the generated code to the validated data
        $validatedData['code'] = $newCode;

        // Save data to database
        diamond::create($validatedData);

        return redirect()->route('new-item.create')->with('success', 'Data saved successfully!');
    }
}
