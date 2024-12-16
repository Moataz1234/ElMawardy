<?php

namespace App\Http\Controllers;

use App\Models\Models;
use App\Models\GoldItem;
use App\Models\GoldItemsAvg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function index()
    {
        $models = Models::with(['goldItems', 'goldItemsAvg'])->get();
        $golditems=GoldItem::where('shop_name', Auth::user()->shop_name)
            ->with(['modelCategory']);
        return view('admin.Gold.models', compact('models','golditems'));
    }
}
