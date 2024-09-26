<?php

namespace App\Http\Controllers\Gold;

use App\Http\Controllers\Controller;
use App\Models\GoldPound;
use Illuminate\Http\Request;

class GoldPoundController extends Controller
{
    /**
     * Display a listing of the gold pounds.
     */
    public function index()
    {
        $goldPounds = GoldPound::all();
        return view('admin.Gold.Gold_pounds_list', compact('goldPounds'));
    }
}

