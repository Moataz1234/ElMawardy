<?php

namespace App\Http\Controllers;

use App\Models\GoldItem;
use App\Models\Order;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Add this import
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request)
    {
        Log::info('Order submission received', ['data' => $request->all()]);

        // Validate the incoming request
        $validatedData = $request->validate([
            'image_link' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order_details' => 'nullable|string',
            'order_kind' => 'required|string',
            'ring_size' => 'nullable|string',
            'weight' => 'nullable|string',
            'gold_color' => 'nullable|string',
            'order_fix_type' => 'required|string',
            'customer_name' => 'required|string',
            'seller_name' => 'required|string',
            'deposit' => 'required|numeric',
            'rest_of_cost' => 'required|numeric',
            'customer_phone' => 'required|string|max:11',
            'order_date' => 'required|date',
            'deliver_date' => 'nullable|date',
            'status' => 'nullable|string',
        ]);
        
        // Get the ID of the logged-in user (assuming the user is the shop owner)
        $shop_id = Auth::user()->id;
    
        Log::info('Logged in shop_id', ['shop_id' => $shop_id]);

        // Get the current shop's orders count and increment the order number for this shop
        $shopOrdersCount = Order::where('shop_id', $shop_id)->count();
        $orderNumber = $shopOrdersCount + 1;
    
        // Handle file upload if the image is provided
        $imagePath = null;
        if ($request->hasFile('image_link')) {
            $imagePath = $request->file('image_link')->store('orders_images', 'public');
            Log::info('Image uploaded successfully', ['image_path' => $imagePath]);

        }
    
        // Create a new order linked to the logged-in user's shop_id
        Order::create([
            'shop_id' => $shop_id,  // Set shop_id to the logged-in user's ID
            'order_number' => $orderNumber,
            'image_link' => $imagePath,
            'order_details' => $request->order_details,
            'order_kind' => $request->order_kind,
            'ring_size' => $request->ring_size,
            'weight' => $request->weight,
            'gold_color' => $request->gold_color,
            'order_fix_type' => $request->order_fix_type,
            'customer_name' => $request->customer_name,
            'seller_name' => $request->seller_name,
            'deposit' => $request->deposit,
            'rest_of_cost' => $request->rest_of_cost,
            'customer_phone' => $request->customer_phone,
            'order_date' => $request->order_date,
            'deliver_date' => $request->deliver_date,
            'status' => $request->status,
        ]);
        Log::info('Order created successfully', ['order' => $orderNumber]);

        // Redirect with success message
        return redirect()->route('orders.create')->with('success', 'Order created successfully!');
    }
    

    
    public function create()
{
  // Fetch all shops to display in the form
  $shops = Shop::all();

  // Fetch distinct kinds and gold colors from GoldItem model
  $kinds = \App\Models\GoldItem::select('kind')->distinct()->pluck('kind');
  $gold_colors = \App\Models\GoldItem::select('gold_color')->distinct()->pluck('gold_color');

  return view('shops.orders.Gold_order', [
      'kinds' => $kinds,
      'gold_colors' => $gold_colors,
  ]);
}
public function indexForRabea()
{
    // Ensure Rabea is authenticated
    $this->authorize('viewAny', Order::class); // You might need to set up authorization

    // Fetch all orders that are associated with shops
    // Assuming Rabea is the user who needs to see all orders
    $orders = Order::all(); // Adjust this if you want to filter orders

    return view('admin.Rabea.orders', compact('orders'));
}
public function show($id)
{

    // Show the order details
    $order = Order::findOrFail($id); // Fetch the order or fail with a 404
    
    $this->authorize('view', $order);
    return view('admin.rabea.orders-show', compact('order')); // Pass the order to the view}
}
}
