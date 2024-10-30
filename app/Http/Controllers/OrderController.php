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

    // Validate the incoming request
    try{
    $validatedData = $request->validate([
        'customer_name' => 'nullable|string',
        'seller_name' => 'nullable|string',
        'order_details' => 'nullable|string',
        'deposit' => 'nullable|numeric',
        'rest_of_cost' => 'nullable|numeric',
        'customer_phone' => 'nullable|string|max:11',
        'order_date' => 'nullable|date',
        'deliver_date' => 'nullable|date',
        'status' => 'nullable|string',
        'payment_method' => 'nullable|string',
        
        // Fields for order items
        'order_kind.*' => 'nullable|string',      // Allow null for order kind
        'order_fix_type' => 'nullable|array', // Allow array
        'order_fix_type.*' => 'nullable|string', // Each element in the array must be a string
       'quantity.*' => 'nullable|integer',       // Allow null for quantity
        'ring_size.*' => 'nullable|integer',      // Allow null for ring size
        'gold_color.*' => 'nullable|string',      // Allow null for gold color
        'weight.*' => 'nullable|numeric',         // Allow null for weight
        'image_link.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048', // Allow null for image
    ]);

    }catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed:', $e->errors());
        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    // Get the logged-in user's shop ID
    $shop_id = Auth::user()->id;

    // Get the current shop's orders count and increment the order number for this shop
    $shopOrdersCount = Order::where('shop_id', $shop_id)->count();
    $orderNumber = $shopOrdersCount + 1;

    // Handle the order details type
    $byCustomer = $request->details_type === 'by_customer' ? $request->order_details : null;
    $byShop = $request->details_type === 'by_shop' ? $request->order_details : null;
    $byTwo = $request->details_type === 'by_two' ? $request->order_details : null;

    // Create the order
    $order = Order::create([
        'shop_id' => $shop_id,
        'order_details' => $request->order_details,
        'order_number' => $orderNumber,
        'customer_name' => $request->customer_name,
        'seller_name' => $request->seller_name,
        'deposit' => $request->deposit,
        'rest_of_cost' => $request->rest_of_cost,
        'customer_phone' => $request->customer_phone,
        'order_date' => $request->order_date,
        'deliver_date' => $request->deliver_date,
        'payment_method' => $request->payment_method,
        'by_customer' => $byCustomer,
        'by_shop' => $byShop,    
        'by_two' => $byTwo,    
        'status' => 'pending',
    ]);
    Log::info('Order created successfully', ['order_id' => $order->id]);

    // Loop through each item and save them to order_items table
    foreach ($request->order_kind as $index => $kind) {
        $imagePath = null;

        // Check if there's an image for this particular item and store it
        if ($request->hasFile("image_link.$index")) {
            $imagePath = $request->file("image_link.$index")->store('order_items_images', 'public');
        }

        // Save the order item with nullable fields
        OrderItem::create([
            'order_id' => $order->id,
            'order_kind' => $kind ?? null,                     // Allow null
            'order_fix_type' => $request->order_fix_type[$index] ?? null,  // Allow null
            'quantity' => $request->quantity[$index] ?? null,  // Allow null
            'ring_size' => $request->ring_size[$index] ?? null, // Allow null
            'gold_color' => $request->gold_color[$index] ?? null, // Allow null
            'weight' => $request->weight[$index] ?? null,      // Allow null
            'image_link' => $imagePath,                        // Can be null
        ]);
    }
    // dd($request->all());

    Log::info('Order created successfully', ['order_id' => $order->id]);

    return redirect()->route('orders.create')->with('success', 'Order created successfully!');
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
