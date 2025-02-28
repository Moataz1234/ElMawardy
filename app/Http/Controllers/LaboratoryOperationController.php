<?php

namespace App\Http\Controllers;

use App\Models\LaboratoryOperation;
use App\Models\LaboratoryDestination;
use Illuminate\Http\Request;

class LaboratoryOperationController extends Controller
{
    public function index()
    {
        $operations = LaboratoryOperation::latest()->paginate(10);
        
        foreach($operations as $operation) {
            // Calculate loss percentage based on inputs and outputs
            $totalInput18 = $operation->inputs->sum(function($input) {
                return ($input->weight * $input->purity) / 750;
            });
            
            $totalOutput18 = $operation->outputs->sum(function($output) {
                return ($output->weight * $output->purity) / 750;
            });
            
            // Calculate loss percentage if there are inputs
            if ($totalInput18 > 0) {
                $operation->loss = (($totalInput18 - $totalOutput18) / $totalInput18) * 100;
            } else {
                $operation->loss = 0;
            }
        }

        return view('laboratory.operations.index', compact('operations'));
    }

    public function create()
    {
        return view('laboratory.operations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'operation_number' => 'required|unique:laboratory_operations',
            'operation_date' => 'required|date',
            // 'notes' => 'nullable|string',
            'inputs' => 'required|array|min:1',
            'inputs.*.weight' => 'required|numeric|min:0',
            'inputs.*.purity' => 'required|numeric|min:0'
        ]);

        // Calculate total input weight
        $totalInputWeight = collect($request->inputs)->sum('weight');

        // Create operation
        $operation = LaboratoryOperation::create([
            'operation_number' => $validated['operation_number'],
            'operation_date' => $validated['operation_date'],
            // 'notes' => $validated['notes'],
            'total_input_weight' => $totalInputWeight,
            'status' => 'active'
        ]);

        // Create inputs
        foreach ($request->inputs as $input) {
            $operation->inputs()->create([
                'weight' => $input['weight'],
                'purity' => $input['purity'],
                'input_date' => $validated['operation_date']
            ]);
        }

        return redirect()->route('laboratory.operations.show', $operation)
            ->with('success', 'Operation created successfully.');
    }

    public function show(LaboratoryOperation $operation)
    {
        // If session values don't exist, initialize them
        if (!session()->has('total_cost') || !session()->has('total_earn')) {
            session(['total_cost' => 0]);
            session(['total_earn' => 0]);
        }

        // Calculate totals for inputs
        $totalInput = $operation->inputs->sum('weight');
        $totalInput18 = $operation->inputs->sum(function($input) {
            return ($input->weight * $input->purity) / 750;
        });
        $totalInput21 = $operation->inputs->sum(function($input) {
            return ($input->weight * $input->purity) / 875;
        });
        $totalInput24 = $operation->inputs->sum(function($input) {
            return ($input->weight * $input->purity) / 1000;
        });

        // Calculate totals for outputs
        $totalOutput = $operation->outputs->sum('weight');
        $totalOutput18 = $operation->outputs->sum(function($output) {
            return ($output->weight * $output->purity) / 750;
        });
        $totalOutput21 = $operation->outputs->sum(function($output) {
            return ($output->weight * $output->purity) / 875;
        });
        $totalOutput24 = $operation->outputs->sum(function($output) {
            return ($output->weight * $output->purity) / 1000;
        });

        // Calculate loss percentage
        $loss = $totalInput18 > 0 ? (($totalInput18 - $totalOutput18) / $totalInput18) * 100 : 0;

        return view('laboratory.operations.show', compact(
            'operation',
            'totalInput', 'totalInput18', 'totalInput21', 'totalInput24',
            'totalOutput', 'totalOutput18', 'totalOutput21', 'totalOutput24',
            'loss'
        ));
    }

    public function addInput(Request $request, LaboratoryOperation $operation)
    {
        if ($operation->status === 'closed') {
            return redirect()->back()->with('error', 'لا يمكن إضافة مدخلات لعملية مغلقة');
        }

        $validated = $request->validate([
            'weight' => 'required|numeric|min:0',
            'purity' => 'required|numeric|min:0',
            'input_date' => 'required|date'
        ]);

        $operation->inputs()->create($validated);
        
        // Only update total input weight, don't calculate loss
        $operation->total_input_weight += $validated['weight'];
        $operation->save();

        return redirect()->back()->with('success', 'تم إضافة المدخل بنجاح');
    }

    public function addOutput(Request $request, LaboratoryOperation $operation)
    {
        if ($operation->status === 'closed') {
            return redirect()->back()->with('error', 'لا يمكن إضافة مخرجات لعملية مغلقة');
        }

        $validated = $request->validate([
            'weight' => 'required|numeric|min:0',
            'purity' => 'required|numeric|min:0',
            'output_date' => 'required|date'
        ]);

        $operation->outputs()->create($validated);
        
        // Only update total output weight, don't calculate loss
        $operation->total_output_weight += $validated['weight'];
        $operation->save();

        return redirect()->back()->with('success', 'تم إضافة المخرج بنجاح');
    }

    public function closeOperation(LaboratoryOperation $operation)
    {
        if ($operation->status === 'active') {
            // Calculate final loss
            $operation->loss = $operation->total_input_weight - $operation->total_output_weight;
            $operation->status = 'closed';
            $operation->save();

            return redirect()->back()->with('success', 'تم إغلاق العملية وحساب الخسية بنجاح');
        }

        return redirect()->back()->with('error', 'العملية مغلقة بالفعل');
    }

    public function edit(LaboratoryOperation $operation)
    {
        if ($operation->status === 'closed') {
            return redirect()->route('laboratory.operations.show', $operation)
                ->with('error', 'لا يمكن تعديل عملية مغلقة');
        }

        return view('laboratory.operations.edit', compact('operation'));
    }

    public function updateCosts(Request $request, LaboratoryOperation $operation)
    {
        if ($operation->status !== 'active') {
            return redirect()->back()->with('error', 'لا يمكن تعديل عملية مغلقة');
        }

        $validated = $request->validate([
            'operation_cost' => 'required|numeric|min:0',
            'operation_earn' => 'required|numeric|min:0'
        ]);

        // Get current totals from session or default to 0
        $totalCost = session('total_cost', 0);
        $totalEarn = session('total_earn', 0);

        // Add new values to totals
        $totalCost += $validated['operation_cost'];
        $totalEarn += $validated['operation_earn'];

        // Store updated totals in session
        session(['total_cost' => $totalCost]);
        session(['total_earn' => $totalEarn]);

        // Calculate the difference (total earn - total cost)
        $operation->operation_cost = $totalEarn - $totalCost;
        $operation->save();

        return redirect()->back()->with('success', 'تم تحديث التكاليف بنجاح');
    }

    public function updateWeights(Request $request, LaboratoryOperation $operation)
    {
        if ($operation->status !== 'active') {
            return redirect()->back()->with('error', 'لا يمكن تعديل عملية مغلقة');
        }

        $validated = $request->validate([
            'silver_weight' => 'required|numeric|min:0',
            'gold_weight' => 'required|numeric|min:0',
            'gold_purity' => 'required|numeric|min:0'
        ]);

        // Update silver weight
        $operation->silver_weight = $validated['silver_weight'];
        
        // Add new gold input
        if ($validated['gold_weight'] > 0) {
            $operation->inputs()->create([
                'weight' => $validated['gold_weight'],
                'purity' => $validated['gold_purity'],
                'input_date' => now()
            ]);

            // Update total input weight
            $operation->total_input_weight += $validated['gold_weight'];
        }

        $operation->save();

        return redirect()->back()->with('success', 'تم تحديث الأوزان بنجاح');
    }
} 