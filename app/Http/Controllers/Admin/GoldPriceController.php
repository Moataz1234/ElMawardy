<?php
// app/Http/Controllers/GoldPriceController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\GoldPrice;

class GoldPriceController extends Controller
{
    public function getGoldPrices()
    {
        // Get the latest gold price entry
        $latestPrice = GoldPrice::latest()->first();

        if ($latestPrice) {
            $latestPrice->makeHidden(['id', 'updated_at']);
        }

        // Return a JSON response with formatted created_at
        return response()->json([
            'data' => $latestPrice ? array_merge(
                $latestPrice->toArray(),
                ['created_at' => $latestPrice->created_at->format('Y-m-d H:i:s')]
            ) : null,
        ]);
    }

    // Show the form with the current gold prices
    public function create()
    {
        // Retrieve the latest gold prices
        $latestGoldPrice = GoldPrice::latest()->first();

        return view('admin/update-prices', compact('latestGoldPrice'));
    }

    // Update the gold prices
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'gold_buy' => 'required|numeric',
            'gold_sell' => 'required|numeric',
            'gold_with_work' => 'required|numeric',
            'percent' => 'required|numeric',
            'dollar_price' => 'required|numeric',
            'gold_in_diamond' => 'required|numeric',
            'shoghl_agnaby' => 'required|numeric',
            'elashfoor' => 'required|numeric',
        ]);


        // 2. Save new gold prices
        $goldPrice = GoldPrice::create($request->all());

        // 3. Send ntfy notification
        $this->sendGoldPriceNotification($goldPrice);

        return redirect()->route('gold_prices.create')->with('success', 'Gold prices updated successfully.');
    }
    private function sendGoldPriceNotification(GoldPrice $goldPrice)
    {
        $channel = 'gold_price'; // ntfy topic/channel

        // Format the message
        
    $message = "Gold prices updated:\n"
    . "- Gold Buy: {$goldPrice->gold_buy}\n"
    . "- Gold Sell: {$goldPrice->gold_sell}\n"
    . "- Gold With Work (***): {$goldPrice->gold_with_work}\n"
    . "- Percent: {$goldPrice->percent}\n"
    . "- Dollar Price: {$goldPrice->dollar_price}\n"
    . "- Gold In Diamond: {$goldPrice->gold_in_diamond}\n"
    . "- Shoghl Ajnaby (**): {$goldPrice->shoghl_agnaby}\n"
    . "- Elashfoor(*): {$goldPrice->elashfoor}";

        try {
            Http::withHeaders([
                'Title' => 'Gold Price Update',
                'Priority' => 'high'
            ])->withBody($message, 'text/plain') // <--- Send raw text
            ->post(env('NTFY_URL') . '/' . $channel);
            
        } catch (\Exception $e) {
            Log::error('Failed to send gold price notification: ' . $e->getMessage());
        }
    }
}
