<?php

namespace App\Http\Controllers;

use App\Models\Model;
use App\Models\GoldItem;
use App\Models\GoldItemsAvg;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $models = Model::with(['goldItems', 'goldItemsAvg'])->get();

        return view('test', compact('models'));
    }
}
