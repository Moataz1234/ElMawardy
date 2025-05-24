<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    GoldItem,
    GoldItemSold,
    Warehouse,
    AddRequest,
    TransferRequest,
    SaleRequest,
    PoundRequest,
    Order,
    OrderItem,
    KasrSale,
    User,
    Shop,
    Models,
    Customer,
    GoldPrice
};
use Illuminate\Support\Facades\DB;

class SuperDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_gold_items' => GoldItem::count(),
            'total_sold_items' => GoldItemSold::count(),
            'total_users' => User::count(),
            'total_shops' => Shop::count(),
            'total_customers' => Customer::count(),
            'total_orders' => Order::count(),
            'pending_add_requests' => AddRequest::where('status', 'pending')->count(),
            'pending_transfer_requests' => TransferRequest::where('status', 'pending')->count(),
            'pending_sale_requests' => SaleRequest::where('status', 'pending')->count(),
            'pending_pound_requests' => PoundRequest::where('status', 'pending')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ];

        return view('super.dashboard', compact('stats'));
    }

    public function users()
    {
        $users = User::with('shop')->paginate(20);
        return view('super.users.index', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $shops = Shop::all();
        return view('super.users.edit', compact('user', 'shops'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'usertype' => 'required|in:admin,user,rabea,Acc,super',
            'shop_name' => 'nullable|string',
            'password' => 'nullable|string|min:8',
        ]);

        $data = $request->only(['name', 'email', 'usertype', 'shop_name']);
        
        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);
        
        return redirect()->route('super.users')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('super.users')->with('success', 'User deleted successfully');
    }

    public function shops()
    {
        $shops = Shop::with('users')->paginate(20);
        return view('super.shops.index', compact('shops'));
    }

    public function editShop($id)
    {
        $shop = Shop::findOrFail($id);
        return view('super.shops.edit', compact('shop'));
    }

    public function updateShop(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $shop->update($request->all());
        
        return redirect()->route('super.shops')->with('success', 'Shop updated successfully');
    }

    public function models()
    {
        $models = Models::orderBy('created_at', 'desc')->paginate(50);
        return view('super.models.index', compact('models'));
    }

    public function goldItems()
    {
        $query = GoldItem::with(['shop', 'modelCategory']);

        // Apply filters
        if (request('search')) {
            $query->where(function($q) {
                $q->where('serial_number', 'like', '%' . request('search') . '%')
                  ->orWhere('model', 'like', '%' . request('search') . '%');
            });
        }

        if (request('shop')) {
            $query->where('shop_name', request('shop'));
        }

        if (request('kind')) {
            $query->where('kind', request('kind'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $goldItems = $query->orderBy('created_at', 'desc')->paginate(50);
        
        return view('super.gold-items.index', compact('goldItems'));
    }

    public function soldItems()
    {
        $query = GoldItemSold::with(['customer', 'modelCategory']);

        // Apply filters
        if (request('search')) {
            $query->where(function($q) {
                $q->where('serial_number', 'like', '%' . request('search') . '%')
                  ->orWhereHas('customer', function($customerQuery) {
                      $customerQuery->where('first_name', 'like', '%' . request('search') . '%')
                                   ->orWhere('last_name', 'like', '%' . request('search') . '%');
                  });
            });
        }

        if (request('shop')) {
            $query->where('shop_name', request('shop'));
        }

        if (request('date_from')) {
            $query->whereDate('sold_date', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->whereDate('sold_date', '<=', request('date_to'));
        }

        if (request('min_price')) {
            $query->where('price', '>=', request('min_price'));
        }

        $soldItems = $query->orderBy('sold_date', 'desc')->paginate(50);
        
        // Calculate today's sales count separately
        $todaysSalesCount = GoldItemSold::whereDate('sold_date', today())->count();
        
        return view('super.sold-items.index', compact('soldItems', 'todaysSalesCount'));
    }

    public function requests()
    {
        $addRequests = AddRequest::orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);
        $transferRequests = TransferRequest::with(['goldItem', 'fromShop', 'toShop'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);
        $saleRequests = SaleRequest::with(['goldItem', 'customer'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);
        $poundRequests = PoundRequest::with(['goldPound', 'shop'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);
        
        // Add transfer request history
        $transferRequestHistory = \App\Models\TransferRequestHistory::with(['fromShop', 'toShop', 'modelDetails'])
            ->orderBy('created_at', 'desc')->paginate(20);

        return view('super.requests.index', compact(
            'addRequests', 
            'transferRequests', 
            'saleRequests', 
            'poundRequests',
            'transferRequestHistory'
        ));
    }

    public function handleRequest(Request $request, $type, $id)
    {
        $action = $request->input('action');
        
        switch($type) {
            case 'add':
                $requestModel = AddRequest::findOrFail($id);
                break;
            case 'transfer':
                $requestModel = TransferRequest::findOrFail($id);
                break;
            case 'sale':
                $requestModel = SaleRequest::findOrFail($id);
                break;
            case 'pound':
                $requestModel = PoundRequest::findOrFail($id);
                break;
            default:
                return back()->withErrors('Invalid request type');
        }

        if ($action === 'approve') {
            $requestModel->status = 'approved';
            $message = ucfirst($type) . ' request approved successfully';
        } elseif ($action === 'reject') {
            $requestModel->status = 'rejected';
            $message = ucfirst($type) . ' request rejected successfully';
        } else {
            return back()->withErrors('Invalid action');
        }

        $requestModel->save();
        
        return back()->with('success', $message);
    }

    // === CUSTOMER MANAGEMENT METHODS ===
    public function customers()
    {
        $customers = Customer::orderBy('created_at', 'desc')->paginate(20);
        return view('super.customers.index', compact('customers'));
    }

    public function createCustomer()
    {
        return view('super.customers.create');
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'payment_method' => 'nullable|string|max:255',
        ]);

        Customer::create($request->all());

        return redirect()->route('super.customers')->with('success', 'Customer created successfully');
    }

    public function showCustomer($id)
    {
        $customer = Customer::with('goldItemsSold')->findOrFail($id);
        return view('super.customers.show', compact('customer'));
    }

    public function editCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        return view('super.customers.edit', compact('customer'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'payment_method' => 'nullable|string|max:255',
        ]);

        $customer->update($request->all());

        return redirect()->route('super.customers')->with('success', 'Customer updated successfully');
    }

    public function deleteCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('super.customers')->with('success', 'Customer deleted successfully');
    }

    // === ORDER MANAGEMENT METHODS ===
    public function orders()
    {
        $orders = Order::with(['shop', 'items'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'in_progress' THEN 1 ELSE 2 END, created_at DESC")
            ->paginate(20);
        
        // Calculate status counts separately
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $inProgressOrdersCount = Order::where('status', 'in_progress')->count();
        $completedOrdersCount = Order::where('status', 'completed')->count();
        
        // Get orders by status for tabs (not paginated to avoid issues)
        $pendingOrders = Order::with(['shop', 'items'])->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $inProgressOrders = Order::with(['shop', 'items'])->where('status', 'in_progress')->orderBy('created_at', 'desc')->get();
        $completedOrders = Order::with(['shop', 'items'])->where('status', 'completed')->orderBy('created_at', 'desc')->get();
        
        return view('super.orders.index', compact(
            'orders', 
            'pendingOrdersCount', 
            'inProgressOrdersCount', 
            'completedOrdersCount',
            'pendingOrders',
            'inProgressOrders', 
            'completedOrders'
        ));
    }

    public function showOrder($id)
    {
        $order = Order::with(['shop', 'items'])->findOrFail($id);
        return view('super.orders.show', compact('order'));
    }

    public function editOrder($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $shops = Shop::all();
        return view('super.orders.edit', compact('order', 'shops'));
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'order_number' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'seller_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order_details' => 'nullable|string',
            'deposit' => 'nullable|numeric',
            'rest_of_cost' => 'nullable|numeric',
            'order_date' => 'nullable|date',
            'deliver_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $order->update($request->all());

        return redirect()->route('super.orders')->with('success', 'Order updated successfully');
    }

    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete(); // Delete associated order items
        $order->delete();

        return redirect()->route('super.orders')->with('success', 'Order deleted successfully');
    }

    public function kasrSales()
    {
        $query = KasrSale::with(['shop', 'items']);

        // Apply filters
        if (request('search')) {
            $query->where(function($q) {
                $q->where('customer_name', 'like', '%' . request('search') . '%')
                  ->orWhere('customer_phone', 'like', '%' . request('search') . '%');
            });
        }

        if (request('shop')) {
            $query->where('shop_name', request('shop'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('date')) {
            $query->whereDate('order_date', request('date'));
        }

        $kasrSales = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);
        
        // Calculate status counts separately
        $pendingKasrCount = KasrSale::where('status', 'pending')->count();
        $completedKasrCount = KasrSale::where('status', 'completed')->count();
        
        return view('super.kasr-sales.index', compact('kasrSales', 'pendingKasrCount', 'completedKasrCount'));
    }

    public function analytics()
    {
        $analytics = [
            'monthly_sales' => GoldItemSold::selectRaw('MONTH(sold_date) as month, COUNT(*) as count, SUM(price) as total')
                ->whereYear('sold_date', date('Y'))
                ->groupBy('month')
                ->get(),
            'shop_performance' => Shop::withCount(['users', 'orders'])
                ->get(),
            'top_models' => Models::withCount('goldItems')
                ->orderBy('gold_items_count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('super.analytics', compact('analytics'));
    }

    public function settings()
    {
        $goldPrices = GoldPrice::latest()->first();
        return view('super.settings', compact('goldPrices'));
    }
} 