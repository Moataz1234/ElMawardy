<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\Models;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\DailyReportMail;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class GoldReportController extends Controller
{
    public function generateDailyReport($date = null)
    {
        // Use the provided date or default to today's date
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // Get all items sold on the selected date, grouped by model
        $soldItems = GoldItemSold::whereDate('sold_date', $date)
            ->get()
            ->groupBy('model');

        if ($soldItems->isEmpty()) {
            return view('Admin/reports/no_sales', [
                'workshop_count' => 0,
                'order_date' => $date->format('d-m-Y'),
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
            $modelInfo = Models::where('model', $model)->first();

            $currentInventory = GoldItem::where('model', $model)->get();

            // Calculate total pieces sold on the selected date for this model
            $totalPiecesSoldToday = $items->sum('quantity');

            // Get unique shop names that sold this model on the selected date
            $shopsWithSales = $items->pluck('shop_name')->unique()->implode(' / ');

            // Calculate shop distribution for current inventory (not sold items)
            $shopDistribution = [];
            $shops = [
                'Mohandessin Shop',
                'Mall of Arabia',
                'Nasr City',
                'Zamalek',
                'Mall of Egypt',
                'EL Guezira Shop',
                'Arkan',
                'District 5',
                'U Venues'
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
               // Calculate last production date and quantity
        $lastProductionDate = null;
        $lastProductionQuantity = 0;

        // Get the latest production date from GoldItem (rest_since) and GoldItemSold (add_date)
        $lastInventoryItem = GoldItem::where('model', $model)
            ->where('talab', false)
            ->whereHas('modelCategory', function ($query) {
                $query->where('source', 'Production');
            })
            ->orderBy('rest_since', 'desc')
            ->first();

        $lastSoldItem = GoldItemSold::where('model', $model)
            ->where('talab', false)
            ->whereHas('modelCategory', function ($query) {
                $query->where('source', 'Production');
            })
            ->orderBy('add_date', 'desc')
            ->first();

        // Determine the latest date between inventory and sold items
        if ($lastInventoryItem && $lastSoldItem) {
            if ($lastInventoryItem->rest_since > $lastSoldItem->add_date) {
                $lastProductionDate = $lastInventoryItem->rest_since;
                $lastProductionQuantity = $lastInventoryItem->quantity;
            } else {
                $lastProductionDate = $lastSoldItem->add_date;
                $lastProductionQuantity = $lastSoldItem->quantity;
            }
        } elseif ($lastInventoryItem) {
            $lastProductionDate = $lastInventoryItem->rest_since;
            $lastProductionQuantity = $lastInventoryItem->quantity;
        } elseif ($lastSoldItem) {
            $lastProductionDate = $lastSoldItem->add_date;
            $lastProductionQuantity = $lastSoldItem->quantity;
        }
            $reportsData[$model] = [

                'workshop_count' => $items->where('shop_name', 'Workshop')->count(),
                'order_date' => $date->format('d-m-Y'),
                'gold_color' => $items->first()->gold_color,
                'source' => $modelInfo ? $modelInfo->source : $items->first()->source, // Get source from Models
                'stars' => $modelInfo ? $modelInfo->stars : $items->first()->stars, // Get source from Models
                'image_path' => $modelInfo ? $modelInfo->scanned_image : null, // Get image from Models
                'model' => $model,
                'remaining' => $remaining,
                'total_production' => $totalProduction,
                'total_sold' => $totalSold,
                'first_sale' => $items->min('sold_date'),
                'last_sale' => $items->max('sold_date'),
                'shop' => $shopsWithSales,
                'pieces_sold_today' => $totalPiecesSoldToday,
                'shops_data' => $shopDistribution,
                'last_production_date' => $lastProductionDate ? Carbon::parse($lastProductionDate)->format('d-m-Y') : null,
                'last_production_quantity' => $lastProductionQuantity
            ];        }

        return $reportsData;
    }
    public function sendDailyReport(Request $request)
    {
        try {
            // Get the selected date and recipients from the request
            $date = $request->input('date');
            $recipients = $request->input('recipients', []);
    
            // Validate recipients
            if (empty($recipients)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No recipients specified'
                ]);
            }
    
            // Fetch sold items for the selected date, grouped by model
            $soldItems = GoldItemSold::whereDate('sold_date', $date)
                ->get()
                ->groupBy('model');
    
            // Check if there are any sales for the selected date
            if ($soldItems->isEmpty()) {
                Log::info('No sales found for the selected date: ' . $date);
    
                return response()->json([
                    'success' => false,
                    'message' => 'No sales reported for ' . Carbon::parse($date)->format('d-m-Y')
                ]);
            }
    
            // Generate the report data for the selected date
            $reportData = $this->generateDailyReport($date);
            Log::info('Report data generated successfully for date: ' . $date);
    
            // Calculate total items sold for the selected day
            $totalItemsSold = GoldItemSold::whereDate('sold_date', $date)->count();
    
            // Generate the PDF using the correct view
            $pdf = PDF::loadView('Admin.Reports.view', [
                'reportsData' => $reportData,
                'selectedDate' => $date,
                'totalItemsSold' => $totalItemsSold,
                'recipients' => $recipients, // Pass recipients to the view
                'isPdf' => true // Add a flag to indicate PDF export
            ]);
            $pdf->setPaper('A4', 'landscape');
            Log::info('PDF generated successfully');
    
            // Send email to the specified recipients
            try {
                Mail::to($recipients)
                    ->send(new DailyReportMail($reportData, $pdf));
                Log::info('Email sent successfully to recipients');
            } catch (\Exception $e) {
                Log::error('Mail sending failed: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                throw $e;
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Report sent successfully for ' . Carbon::parse($date)->format('d-m-Y')
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error in sendDailyReport: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to send report: ' . $e->getMessage()
            ], 500);
        }
    }
}
