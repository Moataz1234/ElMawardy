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

class RabiaController extends Controller
{
    use AuthorizesRequests;

    public function indexForRabea(Request $request)
{
    // Get search input, sorting field, and direction from the request
    $search = $request->input('search');
    $sort = $request->input('sort', 'order_number');
    $direction = $request->input('direction', 'asc');

    // Build the query to exclude pending orders
    $query = Order::where('status', '<>', value: 'pending'); // Exclude pending orders
    $query = Order::where('status', '<>', value: 'خلص'); // Exclude finished orders

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
    
    $kinds = OrderItem::distinct()->pluck('order_kind');

    // Retrieve all unique gold colors if needed (adjust according to your app's needs)
    $gold_colors = OrderItem::distinct()->pluck('gold_color');

    // $this->authorize('view', $order);
    return view('admin.rabea.orders-edit', compact('order', 'kinds', 'gold_colors')); // Pass the order to the view}
}
public function update(Request $request, $id)
{
    // dd('Form submitted!', $request->all());

    // Find the order by ID
    $order = Order::findOrFail($id);

    Log::info($request->all());
    // Update order data
    $order->customer_name = $request->customer_name;
    $order->customer_phone = $request->customer_phone;
    $order->seller_name = $request->seller_name;
    $order->order_details = $request->order_details;
    $order->deposit = $request->deposit;
    $order->rest_of_cost = $request->rest_of_cost;
    $order->payment_method = $request->payment_method;
    $order->order_date = $request->order_date;
    $order->save();
    // dd($request->all());

    // Update order items (loop through and update each item)
    foreach ($request->order_kind as $index => $orderKind) {
        // Find the specific item by index or ID
        $item = $order->items()->get()[$index]; // Retrieve the item using the relationship
        
        // Update item data
        $item->order_kind = $orderKind;
        $item->order_fix_type = $request->order_fix_type[$index];
        $item->quantity = $request->quantity[$index];
        $item->gold_color = $request->gold_color[$index];
        $item->save();
    }
    // Redirect to the orders list or wherever necessary
    return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
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
public function accept(Request $request)
{
    $orderIds = $request->input('order_ids');

    if (!$orderIds) {
        return redirect()->back()->with('error', 'No orders selected.');
    }

    // Update the status of selected orders to 'reviewing'
    Order::whereIn('id', $orderIds)->update(['status' => 'تم الاستلام']);

    return redirect()->back()->with('success', 'Selected orders have been marked for review.');
}

public function updateStatus(Request $request,$id)
{
    $order = Order::findOrFail($id); // Fetch the order by ID
    $this->authorize('update', $order); // Ensure the user is authorized to update the order
    
    if ($order->status == 'خلص') {
        // Redirect to the completed orders view
        return redirect()->route('completed_orders.index')->with('success', 'Order has been marked as completed.');
    }
    // Update the order status
    $order->status = $request->input('status');
    $order->save(); // Save the updated order

    return redirect()->route('orders.rabea.index');
}
public function showCompletedOrders()
{
    $completedOrders = Order::where('status', 'خلص')->get();
    return view('admin.rabea.completed', compact('completedOrders'));
}
public function toPrint(Request $request)
{
    // Start building the query for fetching orders
    $query = Order::where('status', '!=', 'pending');

    // Optional: Filter by today's orders if 'filter' is passed with 'today'
    if ($request->has('filter') && $request->filter === 'today') {
        $query->whereDate('order_date', Carbon::today());
    }

    // Set default sorting values
    $sort = $request->get('sort', 'order_date'); // Default sort by 'order_date'
    $direction = $request->get('direction', 'desc'); // Default direction is 'desc'

    // Apply sorting to the query
    $orders = $query->orderBy($sort, $direction)->paginate(20); // Paginate the results

    // Pass the orders and any other required data to the view
    return view('admin.Rabea.to_print', compact('orders'));
}
}
