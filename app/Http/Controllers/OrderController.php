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
use App\Notifications\NewOrderNotification;

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
            'payment_method' =>'nullable|string',
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
       $order= Order::create([
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
            'status' => 'في انتظار الموافقة عليه',
        ]);
        $rabea = User::where('name', 'Rabea')->first();
        if ($rabea) {
            $rabea->notify(new NewOrderNotification($order));
        }
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
public function indexForRabea(Request $request)
{
    $search = $request->input('search');
    $sort = $request->input('sort', 'order_number');
    $direction = $request->input('direction', 'asc');



    $orders = Order::query()
    ->when($search, function ($query, $search) {
        // Apply search conditions based on the fields provided
        return $query->where('order_number', 'like', "%{$search}%")
            ->orWhere('customer_name', 'like', "%{$search}%")
            ->orWhere('seller_name', 'like', "%{$search}%")
            ->orWhere('order_kind', 'like', "%{$search}%")
            ->orWhere('status', 'like', "%{$search}%");
    })
    // Apply sorting based on user input or defaults
    ->orderBy($sort, $direction)
    ->paginate(20); // Paginate the results

    return view('admin.Rabea.orders', compact('orders'));
}
public function show($id)
{

    // Show the order details
    $order = Order::findOrFail($id); // Fetch the order or fail with a 404
    
    $this->authorize('view', $order);
    return view('admin.rabea.orders-show', compact('order')); // Pass the order to the view}
}
public function edit($id)
{
    // Show the order details
    $order = Order::findOrFail($id); // Fetch the order or fail with a 404
    
    $this->authorize('view', $order);
    return view('admin.rabea.orders-edit', compact('order')); // Pass the order to the view}
}
public function updateOrder(Request $request,$id)
{
    // Show the order details
    $request->validate([
        'order_kind' => 'required|string|max:255',
        'order_fix_type' => 'required|string|max:255',
        'ring_size' => 'nullable|numeric',
        'weight' => 'nullable|numeric',
        'gold_color' => 'nullable|string|max:255',
        'order_details' => 'nullable|string',
        'customer_name' => 'nullable|string|max:255',
        'customer_phone' => 'nullable|string|max:20',
        'seller_name' => 'nullable|string|max:255',
        'deposit' => 'nullable|numeric',
        'rest_of_cost' => 'nullable|numeric',
        'order_date' => 'nullable|date',
        'deliver_date' => 'nullable|date',
        'status' => 'required|string',
        'image_link' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Find the order by ID
    $order = Order::findOrFail($id);

    // Update the order details
    $order->order_kind = $request->input('order_kind');
    $order->order_fix_type = $request->input('order_fix_type');
    $order->ring_size = $request->input('ring_size');
    $order->weight = $request->input('weight');
    $order->gold_color = $request->input('gold_color');
    $order->order_details = $request->input('order_details');
    $order->customer_name = $request->input('customer_name');
    $order->customer_phone = $request->input('customer_phone');
    $order->seller_name = $request->input('seller_name');
    $order->deposit = $request->input('deposit');
    $order->rest_of_cost = $request->input('rest_of_cost');
    $order->order_date = $request->input('order_date');
    $order->deliver_date = $request->input('deliver_date');
    $order->status = $request->input('status');
    if ($request->hasFile('image_link')) {
        $path = $request->file('image_link')->store('public/order_images');
        $order->image_link = str_replace('public/', '', $path); // Save the relative path
    }

    $order->save();
    return redirect()->route('orders.rabea.index')->with('success', 'order updated successfully.');

        
}
public function requests()
{
    $this->authorize('viewAny', Order::class); // Ensure Rabea is authorized

    // Fetch all orders that are pending
    $orders = Order::where('status', 'pending')->get();

    return view('admin.Rabea.orders-requests', compact('orders'));
}
public function acceptOrder(Order $order)
{
    $this->authorize('update', $order); // Ensure Rabea is authorized

    // Change the status to 'in_progress'
    $order->status = 'in_progress';
    $order->save();

    return redirect()->route('orders.requests')->with('success', 'Order accepted and is now in progress.');
}

}
