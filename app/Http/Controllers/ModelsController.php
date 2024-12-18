<?php

namespace App\Http\Controllers;

use App\Models\Models;
use Illuminate\Http\Request;

class ModelsController extends Controller
{
    public function index(Request $request)
    {
        $query = Models::query();

        if ($request->has('search')) {
            $query->where('model', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }

        $models = $query->get();

        return view('admin.Gold.models', compact('models'));
    }

    public function create()
    {
        return view('admin.Gold.create_model');
    }

    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required|string|max:255',
            'SKU' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'first_production' => 'required|date',
            'semi_or_no' => 'required|string|max:255',
            'average_of_stones' => 'required|numeric',
        ]);

        Models::create($request->all());

        return redirect()->route('models.index')->with('success', 'Model added successfully.');
    }

    public function edit(Models $model)
    {
        return view('admin.Gold.edit_model', compact('model'));
    }

    public function update(Request $request, Models $model)
    {
        $request->validate([
            'model' => 'required|string|max:255',
            'SKU' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'first_production' => 'required|date',
            'semi_or_no' => 'required|string|max:255',
            'average_of_stones' => 'required|numeric',
        ]);

        $model->update($request->all());

        return redirect()->route('models.index')->with('success', 'Model updated successfully.');
    }

    public function destroy(Models $model)
    {
        $model->delete();

        return redirect()->route('models.index')->with('success', 'Model deleted successfully.');
    }
}
