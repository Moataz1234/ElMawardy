<?php

namespace App\Http\Controllers;
use App\Models\GoldItem;
use App\Services\GoldItemService;

use Illuminate\Http\Request;

class GoldCatalogController extends Controller
{
    protected $goldItemService;

    public function __construct(GoldItemService $goldItemService)
    {
        $this->goldItemService = $goldItemService;
    }
    public function ThreeView(Request $request)
{
    $goldItems = $this->goldItemService->getShopItems($request);


    return view('admin.Gold.Catalog',  $goldItems);
}

    public function FourView()
    {
    $catalogItems = GoldItem::paginate(36);
    return view('GoldCatalog.AdminView.FourInRow', compact('catalogItems'));
    }

}
