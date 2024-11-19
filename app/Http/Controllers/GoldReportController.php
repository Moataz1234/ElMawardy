<?php
namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\GoldItemSold;
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
        // Calculate total pieces sold today for this model
        $totalPiecesSoldToday = $items->sum('quantity');
        
        // Get unique shop names that sold this model today
        $shopsWithSales = $items->pluck('shop_name')->unique()->implode(' / ');

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
                'shop' => $shopsWithSales,
                'pieces_sold_today' => $totalPiecesSoldToday, // Add total pieces sold today

                // 'sold_prices' => $items->pluck('price')->implode(', '),          

                'shops_data' => $shopDistribution
            ];
        }

        // For PDF generation
            $pdf = PDF::loadView('admin.reports.gold_report', ['reportsData' => $reportsData]);
            $pdf->setPaper('A4');
            return $pdf->stream('dailyreport.pdf');
            // return $reportsData;
    }
    public function sendDailyReport()
    {
        try {
            // Get today's date
            $today = Carbon::today();
            
            // Get all items sold today grouped by model
            $soldItems = GoldItemSold::whereDate('sold_date', $today)
                ->get()
                ->groupBy('model');
    
            // Check if there are any sales today
            if ($soldItems->isEmpty()) {
                Log::info('No sales found for today');

                return response()->json([
                    'success' => false,
                    'message' => 'No sales reported for today ' . $today->format('d-m-Y')
                ]);
            }            // Use the existing generateDailyReport method to get the PDF
            $recipients = [
                'omar@elmawardy.com',
                'moataza630@gmail.com'
            ];
            
            Log::info('Starting report generation process');

        $reportData = $this->generateDailyReport();
        Log::info('Report data generated successfully');

        $pdf = PDF::loadView('admin.reports.gold_report', ['reportsData' => $reportData]);
        $pdf->setPaper('A4');
        Log::info('PDF generated successfully');

        // Test mail configuration
        $config = config('mail');
        Log::info('Mail configuration:', [
            'host' => $config['mailers']['smtp']['host'],
            'port' => $config['mailers']['smtp']['port'],
            'encryption' => $config['mailers']['smtp']['encryption']
        ]);

        // Send email with error catching
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
            'message' => 'Report sent successfully'
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