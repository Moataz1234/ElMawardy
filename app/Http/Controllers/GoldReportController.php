<?php
namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
use Illuminate\Http\Request;

class GoldReportController extends Controller
{
    public function index(Request $request)
    {
        // Check if a model name is provided
        $modelName = $request->input('model');
        
        if ($modelName) {
            // Fetch items for the specific model only
            $items = GoldItem::where('model', 'like', '%' . $modelName . '%')->get();
            $soldItems = GoldItemSold::where('model', 'like', '%' . $modelName . '%')->get();

            // Calculate dynamic data

            // Group data by model (in this case, it will only be the one model)
            $modelsData = $items->groupBy('model')->map(function($modelItems) use ($soldItems) {
                $modelSold = $soldItems->where('model', $modelItems->first()->model)->count(); // Count sold items by model
                $modelRemaining = $modelItems->count(); // Remaining count for that model
                $totalProduction = $modelItems->count()+ $soldItems->count(); // Remaining count for that model
                
                // Get the shops that have this model
                $shopsWithModel = $modelItems->pluck('shop_name')->unique(); // Get unique shop names
                $goldColor = $modelItems->first()->gold_color; // Assuming gold color is the same for all items in this model

                $link = $modelItems->first()->link; // Assuming link is the same for all items in this model
                $source = $modelItems->first()->source; // Assuming link is the same for all items in this model

                return [
                    'source' => $source, // Add link to the data array
                    'link' => $link, // Add link to the data array
                    'total_production' => $totalProduction, // Count of produced items for the model
                    'total_sold' => $modelSold,
                    'remaining' => $modelRemaining,
                    'shops' => $shopsWithModel, // List of shops with the model
                    'gold_color' => $goldColor, // Gold color for the model
                ];
            });
        } else {
            // If no model name is provided, don't fetch any data
            $modelsData = collect();
            $totalProduction = 0;
            $totalSold = 0;
            $remaining = 0;
            $atWorkshop = 0;
            $latestModel = 'No Model Searched';
        }

        return view('Admin.Gold.Report', compact('modelsData'));
    }
}
