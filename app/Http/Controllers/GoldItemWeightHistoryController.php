<?php

namespace App\Http\Controllers;

use App\Models\GoldItemWeightHistory;
use App\Models\GoldItem;
use App\Models\User;
use Illuminate\Http\Request;

class GoldItemWeightHistoryController extends Controller
{
    public function index()
    {
        $weightHistories = GoldItemWeightHistory::with(['user', 'goldItem'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('gold-item-weight-history.index', compact('weightHistories'));
    }
} 