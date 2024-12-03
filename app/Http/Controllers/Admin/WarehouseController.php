<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarehouseRequest;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        $items = Warehouse::where('shop_id', null)->paginate(10);
        return view('admin.warehouse.index', compact('items'));
    }

    public function store(StoreWarehouseRequest $request)
    {
        $this->warehouseService->store($request->validated());
        return redirect()->route('admin.warehouse.index')
            ->with('success', 'Item added to warehouse successfully');
    }

    public function assignToShop(Request $request, $id)
    {
        $item = $this->warehouseService->findItem($id);
        $this->warehouseService->assignToShop(
            $item,
            $request->shop_id,
            $request->shop_name
        );
        return redirect()->back()->with('success', 'Item assigned to shop successfully');
    }
}