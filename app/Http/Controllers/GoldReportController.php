<?php
namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GoldReportController extends Controller
{
    public function index(Request $request)
    {
        // Get the model from request or get all models if none specified
        $modelName = $request->input('model');
        
        if ($modelName) {
            $baseModelName = preg_replace('/-[A-D]$/', '', $modelName);
            $query = GoldItem::where('model', 'like', '%' . $baseModelName . '%');
            $soldQuery = GoldItemSold::where('model', 'like', '%' . $baseModelName . '%');
        } else {
            $query = GoldItem::query();
            $soldQuery = GoldItemSold::query();
        }

        // Get the items
        $items = $query->get();
        $soldItems = $soldQuery->get();

        // Get the first item for header information
        $firstItem = $items->first();

        // Prepare data for your existing view
        $viewData = [
            'workshop_count' => $items->where('shop_name', 'Workshop')->count(),
            'order_date' => $firstItem ? Carbon::parse($firstItem->created_at)->format('d-m-Y') : '',
            'gold_color' => $firstItem ? $firstItem->gold_color : '',
            'source' => $firstItem ? $firstItem->source : 'Production',
            
            // Model section data
            'model' => $firstItem ? $firstItem->model : 'N/A',
            'remaining' => $items->count(),
            'total_production' => $items->count() + $soldItems->count(),
            'total_sold' => $soldItems->count(),
            
            // Production info
            'first_production' => $items->min('created_at') ? Carbon::parse($items->min('created_at'))->format('d-m-Y') : 'N/A',
            'last_production' => $items->max('created_at') ? Carbon::parse($items->max('created_at'))->format('d-m-Y') : 'N/A',
            'shop' => $firstItem ? $firstItem->shop_name : 'N/A',
            'sold_prices' => $soldItems->pluck('price')->implode(', '),
            
            // Shop distribution data
            'shops_data' => [
                'Shop 5' => $this->getShopColorCounts($items, 'Shop 5'),
                'Mall Of Arabia' => $this->getShopColorCounts($items, 'Mall Of Arabia'),
                'Nasr City' => $this->getShopColorCounts($items, 'Nasr City'),
                'Zamalek' => $this->getShopColorCounts($items, 'Zamalek'),
                'Mall Of Egypt' => $this->getShopColorCounts($items, 'Mall Of Egypt'),
                'El Gezera' => $this->getShopColorCounts($items, 'El Gezera'),
                'Arlan' => $this->getShopColorCounts($items, 'Arlan'),
                'District 5' => $this->getShopColorCounts($items, 'District 5'),
                'Uvenues' => $this->getShopColorCounts($items, 'Uvenues'),
            ]
        ];

        return view('Gold_Report', $viewData);
    }

    private function getShopColorCounts($items, $shopName)
    {
        $shopItems = $items->where('shop_name', $shopName);
        
        return [
            'all_rests' => $shopItems->count(),
            'white_gold' => $shopItems->where('gold_color', 'White')->count(),
            'yellow_gold' => $shopItems->where('gold_color', 'Yellow')->count(),
            'rose_gold' => $shopItems->where('gold_color', 'Rose')->count(),
        ];
    }
    public function generateDailyReport()
    {
        // Get today's date
        $today = Carbon::today();
        
        // Get all items sold today grouped by model
        $soldItems = GoldItemSold::whereDate('sold_date', $today)
            ->get()
            ->groupBy('model');
            
            if ($soldItems->isEmpty()) {
            return view('no_sales', [
                'workshop_count' => 0,
                'order_date' => $today->format('d-m-Y'),
                'gold_color' => '',
                'source' => 'Production',
                'model' => 'No Sales Today',
                'remaining' => 0,
                'total_production' => 0,
                'total_sold' => 0,
                'first_production' => '',
                'last_production' => '',
                'shop' => '',
                'sold_prices' => '',
                'shops_data' => []
            ]);
        }

        $reportsData = [];
        
        foreach ($soldItems as $model => $items) {
            $currentInventory = GoldItem::where('model', $model)->get();
        
            // Calculate shop distribution for current inventory (not sold items)
            $shopDistribution = [];
            $shops = [
                'Mohandessin Shop', 'Mall of Arabia', 'Nasr City', 'Zamalek',
                'Mall of Egypt', 'EL Guezira Shop', 'Arkan', 'District 5', 'U Venues'
            ];
    
            foreach ($shops as $shop) {
                $shopItems = $currentInventory->where('shop_name', $shop);
                
                $shopDistribution[$shop] = [
                    'all_rests' => $shopItems->count(), // Total items in this shop
                    'white_gold' => $shopItems->where('gold_color', 'White')->count(),
                    'yellow_gold' => $shopItems->where('gold_color', 'Yellow')->count(),
                    'rose_gold' => $shopItems->where('gold_color', 'Rose')->count()
                ];
            }
    
            // Calculate remaining items (current inventory)
        $remaining = GoldItem::where('model', $model)->count();
        
        // Calculate total sold items
        $totalSold = GoldItemSold::where('model', $model)->count();
        
        // Calculate total production (remaining + total sold)
        $totalProduction = $remaining + $totalSold;

            $reportsData[$model] = [
                'workshop_count' => $items->where('shop_name', 'Workshop')->count(),
                'order_date' => $today->format('d-m-Y'),
                'gold_color' => $items->first()->gold_color,
                'source' => $items->first()->source,
                'image_path' => $items->first()->link, // Add image path from the database
 
                // Model section data
                'model' => $model,
            'remaining' => $remaining,
            'total_production' => $totalProduction,
            'total_sold' => $totalSold,
                
                // Shop distribution
                'first_sale' => $items->min('sold_date'),
                'last_sale' => $items->max('sold_date'),
                'shop' => $items->first()->shop_name,
                'sold_prices' => $items->pluck('price')->implode(', '),          

                'shops_data' => $shopDistribution
            ];
        }

        // For PDF generation
            $pdf = PDF::loadView('gold_report', ['reportsData' => $reportsData]);
            $pdf->setPaper('A4');
            return $pdf->stream('dailyreport.pdf');

        // For web view
        // return view('Gold_Report', ['reportsData' => $reportsData]);
    }


}
