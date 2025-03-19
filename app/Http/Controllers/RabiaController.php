<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\GoldItem;
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
        $sortDirection = $request->input('direction', 'asc');
        $sortField = $request->input('sort', 'created_at');
        $searchType = $request->input('search_type');
        $searchValue = $request->input('search_value');
    
        // Map Arabic field names to database columns
        $sortFieldMap = [
            'رقم_الأوردر' => 'order_number',
            'اسم_العميل' => 'customer_name',
            'اسم_البائع' => 'seller_name',
            'الحالة' => 'status'
        ];
    
        // Convert Arabic sort field to database column name if needed
        if (isset($sortFieldMap[$sortField])) {
            $sortField = $sortFieldMap[$sortField];
        }
    
        // Pass all parameters to repository method
        $orders = $this->orderRepository->getFilteredOrders(
            $sortField,
            $sortDirection,
            $searchType,
            $searchValue
        );
    
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
        $order = Order::with('items')->findOrFail($id);
        return view('rabea.orders-show', compact('order'));
    }
    public function edit($id)
    {
        // Show the order details
        $order = Order::findOrFail($id); // Fetch the order or fail with a 404
        
        // $kinds = OrderItem::distinct()->pluck('order_kind');
        $kinds = GoldItem::select('kind')->distinct()->pluck('kind');

    
        // Retrieve all unique gold colors if needed (adjust according to your app's needs)
        // $gold_colors = OrderItem::distinct()->pluck('gold_color');
    
        // $this->authorize('view', $order);
        return view('rabea.orders-edit', compact('order', 'kinds')); // Pass the order to the view}
    }
    public function update(Request $request, $id)
    {
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

        // Update order items (loop through and update each item)
        foreach ($request->order_kind as $index => $orderKind) {
            $item = $order->items()->get()[$index];
            
            $item->order_kind = $orderKind;
            $item->item_type = $request->item_type[$index];
            $item->weight = $request->weight[$index];
            // Add the new fields
            $item->model = $request->model[$index];
            $item->serial_number = $request->serial_number[$index];
            $item->order_details = $request->order_details[$index];
            $item->order_type = $request->order_type[$index];
            $item->save();
        }
        
        // Redirect to the orders list or wherever necessary
        return redirect()->route('orders.rabea.index')->with('success', 'Order updated successfully.');
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
        // Get pending orders with their items
        $orders = Order::with('items')
            ->where('status', 'في انتظار الموافقة')
            ->get();
        
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
    public function completed(Request $request)
    {
        $searchType = $request->input('search_type');
        $searchValue = $request->input('search_value');
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'asc');
    
        $query = Order::where('status', 'خلص');
    
        // Add search logic
        if ($searchType && $searchValue) {
            $query->where($searchType, 'LIKE', "%{$searchValue}%");
        }
    
        // Add sort logic
        $allowedSortFields = [
            'order_number',
            'customer_name',
            'seller_name',
            'status',
            'created_at'
        ];
    
        if (in_array($sortField, $allowedSortFields)) {
            $orders = $query
                ->orderBy($sortField, $sortDirection)
                ->paginate(20);
        } else {
            $orders = $query
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('rabea.completed', compact('orders'));
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

    try {
        // Update the status of selected orders to 'تم الاستلام'
        Order::whereIn('id', $orderIds)->update(['status' => 'تم الاستلام']);

        return redirect()->back()->with('success', 'تم استلام الطلبات المحددة بنجاح');
    } catch (\Exception $e) {
        Log::error('Error accepting orders: ' . $e->getMessage());
        return redirect()->back()->with('error', 'حدث خطأ أثناء استلام الطلبات');
    }
}
}