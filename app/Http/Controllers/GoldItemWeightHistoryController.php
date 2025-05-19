<?php

namespace App\Http\Controllers;

use App\Models\GoldItemWeightHistory;
use App\Models\GoldItem;
use App\Models\User;
use Illuminate\Http\Request;

class GoldItemWeightHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = GoldItemWeightHistory::with(['user', 'goldItem'])
            ->orderBy('created_at', 'desc');

        $searchSerialNumber = $request->input('search_serial_number');

        if ($searchSerialNumber) {
            $query->whereHas('goldItem', function ($q) use ($searchSerialNumber) {
                $q->where('serial_number', 'like', '%' . $searchSerialNumber . '%');
            });
        }

        $weightHistories = $query->get();

        return view('gold-item-weight-history.index', compact('weightHistories', 'searchSerialNumber'));
    }
} 