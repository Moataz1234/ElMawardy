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

    public function goldItems()
    {
        $goldItems = GoldItem::with(['shop', 'modelCategory'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return view('super.gold-items.index', compact('goldItems'));
    }

    public function soldItems()
    {
        $soldItems = GoldItemSold::with(['customer', 'modelCategory'])
            ->orderBy('sold_date', 'desc')
            ->paginate(50);
        
        return view('super.sold-items.index', compact('soldItems'));
    }

    public function requests()
    {
        $addRequests = AddRequest::orderBy('created_at', 'desc')->paginate(20);
        $transferRequests = TransferRequest::with(['goldItem', 'fromShop', 'toShop'])
            ->orderBy('created_at', 'desc')->paginate(20);
        $saleRequests = SaleRequest::with(['goldItem', 'customer'])
            ->orderBy('created_at', 'desc')->paginate(20);
        $poundRequests = PoundRequest::with(['goldPound', 'shop'])
            ->orderBy('created_at', 'desc')->paginate(20);

        return view('super.requests.index', compact(
            'addRequests', 
            'transferRequests', 
            'saleRequests', 
            'poundRequests'
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

    public function orders()
    {
        $orders = Order::with('shop')->orderBy('created_at', 'desc')->paginate(20);
        return view('super.orders.index', compact('orders'));
    }

    public function kasrSales()
    {
        $kasrSales = KasrSale::with(['shop', 'items'])->orderBy('created_at', 'desc')->paginate(20);
        return view('super.kasr-sales.index', compact('kasrSales'));
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