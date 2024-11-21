<?php
namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\GoldItemRequest;
use App\Services\Admin_GoldItemService;


use App\Models\GoldItem;
use App\Models\Shop;

class GoldItemController extends Controller
{
    protected $goldItemService;

    public function __construct(Admin_GoldItemService $goldItemService)
    {
        $this->goldItemService = $goldItemService;
    }

    public function index(Request $request)
    {
        $goldItems = $this->goldItemService->getGoldItems($request);

        return view('admin.Gold.Inventory_list', [
            'goldItems' => $goldItems,
            'search' => $request->input('search'),
            'sort' => $request->input('sort', 'serial_number'),
            'direction' => $request->input('direction', 'asc')
        ]);
    }
    
    public function create()
    {
        $shops = Shop::all();
        return view('admin.Gold.Create_form', compact('shops'));
    }

    public function store(GoldItemRequest $request)
    {
        $validated = $request->validated();
        $imagePath = $request->hasFile('link') ? $request->file('link')->store('uploads/gold_items', 'public') : null;

        $this->goldItemService->createGoldItem($validated, $imagePath);

        return redirect()->route('gold-items.create')->with('success', 'Gold item added successfully.');
    }

    public function edit(string $id)
    {
        $goldItem = GoldItem::findOrFail($id);
        $shops = Shop::all();

        return view('admin.Gold.Edit_form', compact('goldItem', 'shops'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'link' => 'nullable|file|image',
            'serial_number' => 'nullable|string',
            'shop_name' => 'nullable|string',
            'shop_id' => 'nullable|integer',
            'kind' => 'nullable|string',
            'model' => 'nullable|string',
            'talab' => 'nullable|string',
            'gold_color' => 'nullable|string',
            'stones' => 'nullable|string',
            'metal_type' => 'nullable|string',
            'metal_purity' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'rest_since' => 'nullable|date',
            'source' => 'nullable|string',
            'to_print' => 'nullable|boolean',
            'price' => 'nullable|numeric',
            'semi_or_no' => 'nullable|string',
            'average_of_stones' => 'nullable|numeric',
            'net_weight' => 'nullable|numeric',
        ]);

        $this->goldItemService->updateGoldItem($id, $validated, $request->file('link'));

        return redirect()->route('gold-items.index')->with('success', 'Gold item updated successfully.');
    }


    public function analyzeWeights()
    {
        $analysisData = $this->goldItemService->getWeightAnalysis();
        return view('admin.Gold.WeightAnalysis', $analysisData);
    }
}
