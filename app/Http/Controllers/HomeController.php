<?php

namespace App\Http\Controllers;

use App\Models\Diamond;
use App\Models\GoldItem;
use App\Models\GoldPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->usertype === 'user') {
            // Fetch required data for user dashboard
            $goldItems = GoldItem::with(['shop', 'modelCategory', 'transferRequests'])
                ->paginate(10);
            
                $latestGoldPrice = GoldPrice::latest()->first();
        
            return view('layouts.dashboard', [
                'user' => $user,
                'goldItems' => $goldItems,
                'latestPrices' => $latestGoldPrice,
                'dashboardContent' => 'Shops.Gold.index'
            ]);
        }
        
        switch ($user->usertype) {
            case 'admin':
                $dashboardView = 'admin.dashboard';
                break;
            case 'rabea':
                $dashboardView = 'admin.Rabea.orders';
                break;
            default:
                $dashboardView = 'dashboard.default';
        }

        return view('layouts.dashboard', [
            'user' => $user,
            'dashboardContent' => $dashboardView
        ]);
    }
    public function getShopsWithPieces($modelName)
    {
        $details = Diamond::where('model', $modelName)
                          ->where('condition', 'rest')
                          ->get([
                            'code', 'kind', 'name','cost', 'calico1', 'weight1',
                            'calico2', 'number2', 'weight2', 
                            'calico3', 'number3', 'weight3', 
                            'calico4', 'number4', 'weight4', 
                            'calico5', 'number5', 'weight5', 
                            'calico6', 'number6', 'weight6', 
                         ]);

        return $details;
    }
    public function getPiecesInStore($modelName)
    {
    $count = Diamond::where('model', $modelName)
                      ->where('condition', 'rest')
                      ->count();

    return $count;
    }
    public function searchModel(Request $request)
    {
    $modelName = $request->input('model');
    $piecesInStore = $this->getPiecesInStore($modelName);
    $piecesDetails = $this->getShopsWithPieces($modelName);


    return view('admin.dashboard', [
        'piecesInStore' => $piecesInStore,
        'piecesDetails' => $piecesDetails
    ]);
    }
}
