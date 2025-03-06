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
            return [];
        }

        $reportsData = [];

        foreach ($soldItems as $model => $items) {
            $modelInfo = Models::where('model', $model)->first();

            // Step 1: Get all items for the model from GoldItems and GoldItemsSold
            $inventoryItems = GoldItem::where('model', $model)->get();
            $soldItemsForModel = GoldItemSold::where('model', $model)->get();

            Log::info("Model: $model - Inventory Items: " . $inventoryItems->count());
            Log::info("Model: $model - Sold Items: " . $soldItemsForModel->count());

            // Step 2: Find the latest date from rest_since and add_date
            $latestInventoryDate = $inventoryItems->max('rest_since');
            $latestSoldDate = $soldItemsForModel->max('add_date');

            $latestDate = null;
            if ($latestInventoryDate && $latestSoldDate) {
                $latestDate = $latestInventoryDate > $latestSoldDate ? $latestInventoryDate : $latestSoldDate;
            } elseif ($latestInventoryDate) {
                $latestDate = $latestInventoryDate;
            } elseif ($latestSoldDate) {
                $latestDate = $latestSoldDate;
            }
            Log::info("Model: $model - Latest Date: " . ($latestDate ? $latestDate : 'No Date Found'));

            // Step 3: Calculate the quantity for the latest date or for "old" items
            $lastProductionQuantity = 0;
            $oldItemsQuantity = 0;

            if ($latestDate) {
                // Calculate quantity for the latest date
                $lastProductionQuantity += $inventoryItems
                    ->where('rest_since', $latestDate)
                    ->sum('quantity');

                $lastProductionQuantity += $soldItemsForModel
                    ->where('add_date', $latestDate)
                    ->sum('quantity');
                Log::info("Model: $model - Last Production Quantity: " . $lastProductionQuantity);
            } else {
                // Calculate quantity for items with null dates (old items)
                $oldItemsQuantity += $inventoryItems
                    ->whereNull('rest_since')
                    ->sum('quantity');

                $oldItemsQuantity += $soldItemsForModel
                    ->whereNull('add_date')
                    ->sum('quantity');
                Log::info("Model: $model - Old Items Quantity: " . $oldItemsQuantity);
            }

            // Step 4: Prepare the last production data
            $lastProductionDisplay = $latestDate
                ? Carbon::parse($latestDate)->format('d-m-Y') . ' (Qty: ' . $lastProductionQuantity . ')'
                : 'Old (Qty: ' . $oldItemsQuantity . ')';

            $reportsData[$model] = [
                'workshop_count' => $items->where('shop_name', 'Workshop')->count(),
                'order_date' => $date->format('Y-m-d'),
                'gold_color' => $items->first()->gold_color,
                'source' => $modelInfo ? $modelInfo->source : $items->first()->source,
                'stars' => $modelInfo ? $modelInfo->stars : $items->first()->stars,
                'image_path' => $modelInfo ? $modelInfo->scanned_image : null,
                'model' => $model,
                'remaining' => GoldItem::where('model', $model)->count(),
                'total_production' => GoldItem::where('model', $model)->count() + GoldItemSold::where('model', $model)->count(),
                'total_sold' => GoldItemSold::where('model', $model)->count(),
                // 'first_sale' => $modelInfo ? $modelInfo->first_production : $items->first()->first_production,
                // 'last_sale' => $items->max('sold_date'),
                'shop' => $items->pluck('shop_name')->unique()->implode(' / '),
                'pieces_sold_today' => $items->count(),
                'shops_data' => $this->getShopDistribution($model),
                'first_production' => $modelInfo && $modelInfo->first_production
                    ? $modelInfo->first_production
                    : 'Old',
                'last_production' => $lastProductionDisplay
            ];
        }

        return $reportsData;
    }

    /**
     * Helper method to get shop distribution for a specific model.
     */
    private function getShopDistribution($model)
    {
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

        $shopDistribution = [];

        foreach ($shops as $shop) {
            $shopItems = GoldItem::where('model', $model)->where('shop_name', $shop)->get();

            $shopDistribution[$shop] = [
                'all_rests' => $shopItems->count(),
                'white_gold' => $shopItems->where('gold_color', 'White')->count(),
                'yellow_gold' => $shopItems->where('gold_color', 'Yellow')->count(),
                'rose_gold' => $shopItems->where('gold_color', 'Rose')->count()
            ];
        }

        return $shopDistribution;
    }

    public function sendDailyReport(Request $request)
    {
        try {
            $date = $request->input('date');
            $recipients = $request->input('recipients', []);

            if (empty($recipients)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No recipients specified'
                ]);
            }

            // Generate the report data
            $reportsData = $this->generateDailyReport($date);
            $totalItemsSold = GoldItemSold::whereDate('sold_date', $date)->count();
            $formattedDate = Carbon::parse($date)->format('d-m-Y');

            // Generate PDF using the same view as the web display
            $pdf = PDF::loadView('Admin.Reports.view', [
                'reportsData' => $reportsData,
                'selectedDate' => $date,
                'totalItemsSold' => $totalItemsSold,
                'recipients' => $recipients,
                'isPdf' => true  // This flag will hide web-only elements
            ]);

            $pdf->setPaper('A4', 'landscape');

            try {
                // Send email with the PDF attachment
                Mail::to($recipients)
                    ->send(new DailyReportMail($reportsData, $pdf, $formattedDate));

                Log::info('Email sent successfully to recipients for date: ' . $formattedDate);
            } catch (\Exception $e) {
                Log::error('Mail sending failed: ' . $e->getMessage());
                throw $e;
            }

            return response()->json([
                'success' => true,
                'message' => 'Report sent successfully for ' . $formattedDate
            ]);
        } catch (\Exception $e) {
            Log::error('Error in sendDailyReport: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send report: ' . $e->getMessage()
            ], 500);
        }
    }
}
