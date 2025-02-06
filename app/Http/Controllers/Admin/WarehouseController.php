<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\AddRequest;
use App\Models\Shop;
use App\Models\DeletedItemHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function index()
    {
        $items = Warehouse::paginate(10);
        $shops = Shop::all();
        return view('admin.warehouse_index', compact('items', 'shops'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $selectedItems = $request->input('selected_items', []);

        if (empty($selectedItems)) {
            return redirect()->back()->with('error', 'No items selected.');
        }

        if ($action === 'delete') {
            $this->storeDeletedItems($selectedItems, 'Bulk deletion');
            Warehouse::whereIn('id', $selectedItems)->delete();
            return redirect()->back()->with('success', 'Selected items stored in deletion history.');
        } elseif ($action === 'assign') {
            $request->validate([
                'shop_id' => 'required|exists:shops,id',
            ]);

            foreach ($selectedItems as $id) {
                $item = Warehouse::findOrFail($id);

                AddRequest::create([
                    'serial_number' => $item->serial_number,
                    'model' => $item->model,
                    'shop_id' => $request->shop_id,
                    'shop_name' => Shop::find($request->shop_id)->name,
                    'kind' => $item->kind,
                    'gold_color' => $item->gold_color,
                    'metal_type' => $item->metal_type,
                    'metal_purity' => $item->metal_purity,
                    'quantity' => $item->quantity,
                    'weight' => $item->weight,
                    'talab' => $item->talab,
                    'status' => 'pending'
                ]);

                $item->delete();
            }

            return redirect()->back()->with('success', 'Selected items assigned to shop successfully.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    private function storeDeletedItems(array $selectedItems, $reason)
    {
        foreach ($selectedItems as $id) {
            $item = Warehouse::findOrFail($id);

            DeletedItemHistory::create([
                'item_id' => $item->id,
                'deleted_by' => Auth::id(),
                'serial_number' => $item->serial_number,
                'shop_name' => $item->shop_name,
                'kind' => $item->kind,
                'model' => $item->model,
                'gold_color' => $item->gold_color,
                'metal_purity' => $item->metal_purity,
                'weight' => $item->weight,
                'deletion_reason' => $reason,
                'deleted_at' => now(),
            ]);
        }
    }
}
