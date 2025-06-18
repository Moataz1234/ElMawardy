<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\Models;
use App\Models\AddRequest;
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
            // Check if this is a model with a character variant (A, B, C, D)
            $hasVariant = preg_match('/(.*)-([A-D])$/', $model, $matches);
            $baseModel = $hasVariant ? $matches[1] : $model;
            $variant = $hasVariant ? $matches[2] : null;
            
            $modelInfo = Models::where('model', $model)->first();
            
            // If no model info found and this is a variant, try the base model
            if (!$modelInfo && $hasVariant) {
                $modelInfo = Models::where('model', $baseModel)->first();
            }

            // Get workshop data from for_production table
            $workshopData = \App\Models\ForProduction::where('model', $model)
                ->orWhere('model', $baseModel)
                ->first();

            // Determine which variants exist for this base model
            $existingVariants = [];
            foreach (['A', 'B', 'C', 'D'] as $variantLetter) {
                $variantModel = $baseModel . '-' . $variantLetter;
                // Check if variant exists in Models table or has inventory/sold items
                if (Models::where('model', $variantModel)->exists() || 
                    GoldItem::where('model', $variantModel)->exists() || 
                    GoldItemSold::where('model', $variantModel)->exists()) {
                    $existingVariants[] = $variantLetter;
                }
            }

            // Step 1: Get all items for the model from GoldItems, GoldItemsSold, and AddRequests
            $inventoryItems = GoldItem::where('model', $model)->get();
            $soldItemsForModel = GoldItemSold::where('model', $model)->get();
            $addRequestItems = AddRequest::where('model', $model)->get();

            Log::info("Model: $model - Inventory Items: " . $inventoryItems->count());
            Log::info("Model: $model - Sold Items: " . $soldItemsForModel->count());
            Log::info("Model: $model - Add Request Items: " . $addRequestItems->count());

            // Step 2: Find the latest date from rest_since (GoldItems, AddRequests) and add_date (GoldItemsSold)
            // Only consider items where talab = 0
            $latestInventoryDate = $inventoryItems->where('talab', 0)->max('rest_since');
            $latestSoldDate = $soldItemsForModel->where('talab', 0)->max('add_date');
            $latestAddRequestDate = $addRequestItems->where('talab', 0)->max('rest_since');

            $latestDate = null;
            $dates = array_filter([$latestInventoryDate, $latestSoldDate, $latestAddRequestDate]);
            if (!empty($dates)) {
                $latestDate = max($dates);
            }
            Log::info("Model: $model - Latest Date: " . ($latestDate ? $latestDate : 'No Date Found'));

            // Step 3: Calculate the quantity for the latest date or for "old" items
            $lastProductionQuantity = 0;
            $oldItemsQuantity = 0;

            if ($latestDate) {
                // Calculate quantity for the latest date where talab = 0
                $lastProductionQuantity += $inventoryItems
                    ->where('rest_since', $latestDate)
                    ->where('talab', 0)
                    ->sum('quantity');

                $lastProductionQuantity += $soldItemsForModel
                    ->where('add_date', $latestDate)
                    ->where('talab', 0)
                    ->sum('quantity');

                $lastProductionQuantity += $addRequestItems
                    ->where('rest_since', $latestDate)
                    ->where('talab', 0)
                    ->sum('quantity');
                Log::info("Model: $model - Last Production Quantity: " . $lastProductionQuantity);
            } else {
                // Calculate quantity for items with null dates (old items) where talab = 0
                $oldItemsQuantity += $inventoryItems
                    ->whereNull('rest_since')
                    ->where('talab', 0)
                    ->sum('quantity');

                $oldItemsQuantity += $soldItemsForModel
                    ->whereNull('add_date')
                    ->where('talab', 0)
                    ->sum('quantity');

                $oldItemsQuantity += $addRequestItems
                    ->whereNull('rest_since')
                    ->where('talab', 0)
                    ->sum('quantity');
                Log::info("Model: $model - Old Items Quantity: " . $oldItemsQuantity);
            }

            // Step 4: Prepare the last production data
            $lastProductionDisplay = $latestDate
                ? Carbon::parse($latestDate)->format('d-m-Y') . ' (Qty: ' . $lastProductionQuantity . ')'
                : 'Old (Qty: ' . $oldItemsQuantity . ')';

            // Define our variant color mappings
            $variantColors = [
                'A' => 'Black',
                'B' => 'Yellow',
                'C' => 'Red',
                'D' => 'Blue'
            ];

            $reportsData[$model] = [
                'workshop_count' => $items->where('shop_name', 'Workshop')->count(),
                'order_date' => $date->format('Y-m-d'),
                'gold_color' => $items->first()->gold_color,
                'source' => $modelInfo ? $modelInfo->source : $items->first()->source,
                'stars' => $modelInfo ? $modelInfo->stars : $items->first()->stars,
                'image_path' => $modelInfo ? $modelInfo->scanned_image : null,
                'model' => $model,
                'base_model' => $baseModel,
                'variant' => $variant,
                'variant_color' => $variant ? $variantColors[$variant] : null,
                'existing_variants' => $existingVariants,
                'remaining' => GoldItem::where('model', $model)->count(),
                'total_production' => GoldItem::where('model', $model)->count() + GoldItemSold::where('model', $model)->count(),
                'total_sold' => GoldItemSold::where('model', $model)->count(),
                // 'first_sale' => $modelInfo ? $modelInfo->first_production : $items->first()->first_production,
                // 'last_sale' => $items->max('sold_date'),
                'shop' => $items->pluck('shop_name')->unique()->implode(' / '),
                'pieces_sold_today' => $items->count(),
                'shops_data' => $this->getShopDistribution($model, $baseModel),
                'first_production' => $modelInfo && $modelInfo->first_production
                    ? $modelInfo->first_production
                    : 'Old',
                'last_production' => $lastProductionDisplay,
                'workshop_data' => $workshopData ? [
                    'not_finished' => $workshopData->not_finished,
                    'order_date' => $workshopData->order_date->format('d-m-Y')
                ] : [
                    'not_finished' => 0,
                    'order_date' => 'No Order'
                ],
            ];
        }

        return $reportsData;
    }

    /**
     * Helper method to get shop distribution for a specific model.
     */
    private function getShopDistribution($model, $baseModel = null)
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

        // Use base model if provided, otherwise extract from model
        if (!$baseModel) {
            $baseModel = preg_replace('/-[A-D]$/', '', $model);
        }

        $shopDistribution = [];

        foreach ($shops as $shop) {
            // Get the base model items ONLY (not variants)
            $shopItems = GoldItem::where('model', $baseModel)->where('shop_name', $shop)->get();

            // Get variant items for all variants (for counting only)
            $variantAItems = GoldItem::where('model', $baseModel . '-A')->where('shop_name', $shop)->get();
            $variantBItems = GoldItem::where('model', $baseModel . '-B')->where('shop_name', $shop)->get();
            $variantCItems = GoldItem::where('model', $baseModel . '-C')->where('shop_name', $shop)->get();
            $variantDItems = GoldItem::where('model', $baseModel . '-D')->where('shop_name', $shop)->get();

            // Calculate gold color counts ONLY from base model
            $whiteGold = $shopItems->where('gold_color', 'White')->count();
            $yellowGold = $shopItems->where('gold_color', 'Yellow')->count();
            $roseGold = $shopItems->where('gold_color', 'Rose')->count();

            // Get variant counts by character (for All Rests column only)
            $variantA = $variantAItems->count();
            $variantB = $variantBItems->count();
            $variantC = $variantCItems->count();
            $variantD = $variantDItems->count();

            // Initialize shop data
            $shopData = [
                'white_gold' => $whiteGold,
                'yellow_gold' => $yellowGold,
                'rose_gold' => $roseGold,
                'variant_white' => 0, // Not used anymore since we only show base model colors
                'variant_yellow' => 0, // Not used anymore since we only show base model colors
                'variant_rose' => 0, // Not used anymore since we only show base model colors
                'variant_A' => $variantA,
                'variant_B' => $variantB,
                'variant_C' => $variantC,
                'variant_D' => $variantD,
            ];

            // For all_rests: base model total (sum of all gold colors) + all variants
            $baseModelTotal = $whiteGold + $yellowGold + $roseGold;
            $shopData['all_rests'] = $baseModelTotal + $variantA + $variantB + $variantC + $variantD;

            $shopDistribution[$shop] = $shopData;
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

            $pdf->setPaper('A4', 'portrait');

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
