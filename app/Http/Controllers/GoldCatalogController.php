<?php

namespace App\Http\Controllers;
use App\Models\GoldItem;

use Illuminate\Http\Request;

class GoldCatalogController extends Controller
{
    public function ThreeView()
    {
    $catalogItems = GoldItem::paginate(36);
    return view('GoldCatalog.AdminView.ThreeInRow', compact('catalogItems'));
    }  

    public function FourView()
    {
    $catalogItems = GoldItem::paginate(36);
    return view('GoldCatalog.AdminView.FourInRow', compact('catalogItems'));
    }

    ////////////////////////////////////////////Search/////////////////////////////////

    public function Search(Request $request)
    {
        $search = $request ->input('query');
        $result = GoldItem::where('FileName', 'like',  '%' . $search .'%')->
        orWhere('Kind', 'like',  $search )->
        paginate(36);
        $result->appends(['query' => $search]); 
        return view('GoldCatalog.Shared.search_results', compact('result'));
    }

}
