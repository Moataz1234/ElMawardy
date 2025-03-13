<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\Order;
use App\Models\User;
use App\Models\Shop;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Add this import
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Notifications\NewOrderNotification;

class OrderController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request)
    {
        try {
            // Add detailed logging at the start
            Log::info('Starting order creation', ['request_data' => $request->all()]);

            // Validate the incoming request
            $validatedData = $request->validate([
                'customer_name' => 'required|string',
                'seller_name' => 'required|string',
                'deposit' => 'nullable|numeric',
                'rest_of_cost' => 'nullable|numeric',
                'customer_phone' => 'nullable|string|max:11',
                'order_date' => 'required|date',
                'payment_method' => 'nullable|string',
                
                'order_kind' => 'required|array',
                'order_kind.*' => 'required|string',
                'item_type' => 'required|array',
                'item_type.*' => 'required|string',
                'order_details' => 'required|array',
                'order_details.*' => 'required|string',
                'quantity.*' => 'nullable|integer',
                'ring_size.*' => 'nullable|integer',
                'gold_color.*' => 'nullable|string',
                'weight.*' => 'nullable|numeric',
                'image_link.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'order_type.*' => 'required|in:by_customer,by_shop',
            ]);

            // Get the logged-in user's shop ID
            $shop_id = Auth::user()->shop_id;
            if (!$shop_id) {
                throw new \Exception('User does not have an associated shop');
            }

            // Get the current shop's orders count and increment the order number for this shop
            $shopOrdersCount = Order::where('shop_id', $shop_id)->count();
            $orderNumber = $shopOrdersCount + 1;

            // Create the order
            $order = Order::create([
                'shop_id' => $shop_id,
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'seller_name' => $request->seller_name,
                'deposit' => $request->deposit,
                'rest_of_cost' => $request->rest_of_cost,
                'customer_phone' => $request->customer_phone,
                'order_date' => $request->order_date,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);

            Log::info('Order created successfully', ['order_id' => $order->id]);

            // Loop through each item and save them to order_items table
            foreach ($request->order_kind as $index => $kind) {
                try {
                    $imagePath = null;

                    // Check if there's an image for this particular item and store it
                    if ($request->hasFile("image_link.$index")) {
                        $imagePath = $request->file("image_link.$index")->store('order_items_images', 'public');
                    }

                    // Save the order item
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'order_kind' => $kind,
                        // 'order_fix_type' => $request->order_fix_type[$index],
                        'order_details' => $request->order_details[$index],
                        'quantity' => $request->quantity[$index] ?? null,
                        'ring_size' => $request->ring_size[$index] ?? null,
                        'item_type' => $request->item_type[$index] ?? null,
                        'weight' => $request->weight[$index] ?? null,
                        'image_link' => $imagePath,
                        'order_type' => $request->order_type[$index],
                    ]);

                    Log::info('Order item created successfully', [
                        'order_id' => $order->id,
                        'item_id' => $orderItem->id
                    ]);

                } catch (\Exception $e) {
                    Log::error('Failed to create order item', [
                        'order_id' => $order->id,
                        'index' => $index,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order saved successfully'
            ]);

        } catch (\Exception $e) {
            // Add detailed error logging
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving order: ' . $e->getMessage()
            ], 500);
        }
    }
    public function create()
    {
      // Fetch all shops to display in the form
      $shops = Shop::all();

      // Fetch distinct kinds and gold colors from GoldItem model
      $kinds = GoldItem::select('kind')->distinct()->pluck('kind');
      $gold_colors = GoldItem::select('gold_color')->distinct()->pluck('gold_color');

      return view('shops.orders.Gold_order', [
          'kinds' => $kinds,
          'gold_colors' => $gold_colors,
      ]);
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $shop = Shop::where('name', $user->shop_name)->first(); // Assuming the Shop model has the name column
        if (!$shop) {
            return redirect()->back()->with('error', 'Shop not found for the user.');
        }
        // Get search input, sorting field, and direction from the request
        $search = $request->input('search');
        $sort = $request->input('sort', 'order_number');
        $direction = $request->input('direction', 'asc');

        // Build the query to exclude pending orders
        $query = Order::where('shop_id', $shop->id) // Filter by the shop ID associated with the user
        ->where('status', '<>', 'pending') // Exclude pending orders
        ->where('status', '<>', 'خلص');   // Exclude finished orders

        // Apply search conditions if a search term is provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('seller_name', 'like', "%{$search}%")
                  ->orWhere('order_kind', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Apply sorting based on user input or defaults
        $orders = $query->orderBy($sort, $direction)->paginate(20); // Paginate the results

        // Pass the orders and any other required data to the view
        return view('Shops.orders.index', compact('orders'));
    }
    public function showCompletedOrders()
    {
        $user = Auth::user();
        $shop = Shop::where('name', $user->shop_name)->first(); // Assuming the Shop model has the name column
        if (!$shop) {
            return redirect()->back()->with('error', 'Shop not found for the user.');
        }
        $completedOrders = Order::where('shop_id', $shop->id)
        ->where('status', 'خلص')
        ->get();
        return view('shops.orders.completed_orders', compact('completedOrders'));
    }

}
