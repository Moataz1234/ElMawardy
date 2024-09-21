<?php

namespace App\Http\Controllers;

use App\Models\Diamond;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        return view('admin.dashboard');    
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
