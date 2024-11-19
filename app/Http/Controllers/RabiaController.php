<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderItem;

use Illuminate\Support\Facades\Log;
use App\Services\OrderService;
use App\Http\Requests\OrderUpdateRequest;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Add this import

class RabiaController extends Controller
{
    use AuthorizesRequests;

    protected $orderService;
    protected $orderRepository;

    public function __construct(OrderService $orderService, OrderRepository $orderRepository)
    {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
    }

   public function indexForRabea(Request $request)
{
    $sortField = $request->input('sort', 'created_at');
    $sortDirection = $request->input('direction', 'desc');
    $orders = $this->orderRepository->getFilteredOrders($sortField,$sortDirection);

    return view('Rabea.orders_index', compact('orders'));
}
public function updateStatusBulk(Request $request)
{
    $status = $request->input('status');
    $orderIds = json_decode($request->input('order_ids'));

    try {
        // Update all selected orders
        Order::whereIn('id', $orderIds)->update(['status' => $status]);

        return redirect()->back()->with('success', 'Orders status updated successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update orders status');
    }
}
    public function show($id)
    {
    
        // Show the order details
        $order = Order::findOrFail($id); // Fetch the order or fail with a 404
        
        $this->authorize('view', $order);
        return view('rabea.orders-show', compact('order')); // Pass the order to the view}
    }
    public function edit($id)
    {
        // Show the order details
        $order = Order::findOrFail($id); // Fetch the order or fail with a 404
        
        $kinds = OrderItem::distinct()->pluck('order_kind');
    
        // Retrieve all unique gold colors if needed (adjust according to your app's needs)
        $gold_colors = OrderItem::distinct()->pluck('gold_color');
    
        // $this->authorize('view', $order);
        return view('rabea.orders-edit', compact('order', 'kinds', 'gold_colors')); // Pass the order to the view}
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
    public function updateOrder(OrderUpdateRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $this->orderService->updateOrderDetails($order, $request->validated());
        
        return redirect()->route('orders.rabea.index')
            ->with('success', 'Order updated successfully.');
    }

    public function requests()
    {
        $this->authorize('viewAny', Order::class);
        $orders = $this->orderRepository->getPendingOrders();
        
        return view('Rabea.orders-requests', compact('orders'));
    }

    public function toPrint(Request $request)
    {
        $orders = $this->orderRepository->getToPrintOrders(
            $request->get('filter'),
            $request->get('sort', 'order_date'),
            $request->get('direction', 'desc')
        );

        return view('Rabea.to_print', compact('orders'));
    }
    public function completed()
    {
        $completedOrders = Order::where('status', 'خلص')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('rabea.completed', compact('completedOrders'));
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
}