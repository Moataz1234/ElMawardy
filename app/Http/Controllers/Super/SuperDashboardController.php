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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

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
        $query = User::with('shop');

        // Apply filters
        if (request('search')) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('email', 'like', '%' . request('search') . '%');
            });
        }

        if (request('usertype')) {
            $query->where('usertype', request('usertype'));
        }

        if (request('shop_name')) {
            $query->where('shop_name', request('shop_name'));
        }

        // Handle export
        if (request('export') === 'excel') {
            return $this->exportUsersToExcel($query->get());
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
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
        $query = Shop::with('users');

        // Apply filters
        if (request('search')) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('address', 'like', '%' . request('search') . '%');
            });
        }

        // Handle export
        if (request('export') === 'excel') {
            return $this->exportShopsToExcel($query->get());
        }

        $shops = $query->orderBy('created_at', 'desc')->paginate(20);
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
        $query = Models::query();

        // Apply filters
        if (request('search')) {
            $query->where(function($q) {
                $q->where('model', 'like', '%' . request('search') . '%')
                  ->orWhere('SKU', 'like', '%' . request('search') . '%');
            });
        }

        if (request('stars')) {
            $query->where('stars', request('stars'));
        }

        if (request('source')) {
            $query->where('source', request('source'));
        }

        if (request('semi_or_no')) {
            $query->where('semi_or_no', request('semi_or_no'));
        }

        // Handle export
        if (request('export') === 'excel') {
            return $this->exportModelsToExcel($query->get());
        }

        $models = $query->orderBy('created_at', 'desc')->paginate(50);
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

        if (request('weight_min')) {
            $query->where('weight', '>=', request('weight_min'));
        }

        if (request('weight_max')) {
            $query->where('weight', '<=', request('weight_max'));
        }

        // Handle export
        if (request('export') === 'excel') {
            return $this->exportGoldItemsToExcel($query->get());
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

        // Handle export
        if (request('export') === 'excel') {
            return $this->exportSoldItemsToExcel($query->get());
        }

        $soldItems = $query->orderBy('sold_date', 'desc')->paginate(50);
        
        // Calculate today's sales count separately
        $todaysSalesCount = GoldItemSold::whereDate('sold_date', today())->count();
        
        return view('super.sold-items.index', compact('soldItems', 'todaysSalesCount'));
    }

    public function requests()
    {
        // Helper function to apply common filters
        $applyFilters = function($query) {
            if (request('search')) {
                $query->where(function($q) {
                    $q->where('serial_number', 'like', '%' . request('search') . '%')
                      ->orWhere('model', 'like', '%' . request('search') . '%');
                });
            }

            if (request('shop')) {
                $query->where('shop_name', request('shop'));
            }

            if (request('status')) {
                $query->where('status', request('status'));
            }

            if (request('date_from')) {
                $query->whereDate('created_at', '>=', request('date_from'));
            }

            if (request('date_to')) {
                $query->whereDate('created_at', '<=', request('date_to'));
            }
        };

        // Handle export requests
        if (request('export')) {
            return $this->handleRequestsExport(request('export'));
        }

        // Apply filters to each request type
        $addRequestsQuery = AddRequest::query();
        $applyFilters($addRequestsQuery);
        $addRequests = $addRequestsQuery->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);

        $transferRequestsQuery = TransferRequest::with(['goldItem', 'fromShop', 'toShop']);
        $applyFilters($transferRequestsQuery);
        $transferRequests = $transferRequestsQuery->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);

        $saleRequestsQuery = SaleRequest::with(['goldItem', 'customer']);
        if (request('search')) {
            $saleRequestsQuery->where(function($q) {
                $q->where('item_serial_number', 'like', '%' . request('search') . '%')
                  ->orWhereHas('customer', function($customerQuery) {
                      $customerQuery->where('first_name', 'like', '%' . request('search') . '%')
                                   ->orWhere('last_name', 'like', '%' . request('search') . '%');
                  });
            });
        }
        if (request('shop')) {
            $saleRequestsQuery->where('shop_name', request('shop'));
        }
        if (request('status')) {
            $saleRequestsQuery->where('status', request('status'));
        }
        if (request('date_from')) {
            $saleRequestsQuery->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $saleRequestsQuery->whereDate('created_at', '<=', request('date_to'));
        }
        $saleRequests = $saleRequestsQuery->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);

        $poundRequestsQuery = PoundRequest::with(['goldPound', 'shop']);
        $applyFilters($poundRequestsQuery);
        $poundRequests = $poundRequestsQuery->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END, created_at DESC")->paginate(20);
        
        // Add transfer request history
        $transferRequestHistoryQuery = \App\Models\TransferRequestHistory::with(['fromShop', 'toShop', 'modelDetails']);
        if (request('search')) {
            $transferRequestHistoryQuery->where(function($q) {
                $q->where('serial_number', 'like', '%' . request('search') . '%')
                  ->orWhere('model', 'like', '%' . request('search') . '%');
            });
        }
        if (request('shop')) {
            $transferRequestHistoryQuery->where(function($q) {
                $q->where('from_shop_name', request('shop'))
                  ->orWhere('to_shop_name', request('shop'));
            });
        }
        if (request('date_from')) {
            $transferRequestHistoryQuery->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $transferRequestHistoryQuery->whereDate('created_at', '<=', request('date_to'));
        }
        $transferRequestHistory = $transferRequestHistoryQuery->orderBy('created_at', 'desc')->paginate(20);

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
        $query = Order::with(['shop', 'items']);

        // Apply filters
        if (request('search')) {
            $query->where(function($q) {
                $q->where('order_number', 'like', '%' . request('search') . '%')
                  ->orWhere('customer_name', 'like', '%' . request('search') . '%');
            });
        }

        if (request('shop')) {
            $query->where('shop_id', request('shop'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('date_from')) {
            $query->whereDate('order_date', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->whereDate('order_date', '<=', request('date_to'));
        }

        // Handle export
        if (request('export') === 'excel') {
            return $this->exportOrdersToExcel($query->get());
        }

        $orders = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'in_progress' THEN 1 ELSE 2 END, created_at DESC")
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

        if (request('min_price')) {
            $query->where('offered_price', '>=', request('min_price'));
        }

        // Handle export
        if (request('export') === 'excel') {
            return $this->exportKasrSalesToExcel($query->get());
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

    // === EXPORT METHODS ===
    
    private function exportUsersToExcel($users)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['ID', 'Name', 'Email', 'User Type', 'Shop Name', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        
        // Add data
        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->id);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, $user->usertype);
            $sheet->setCellValue('E' . $row, $user->shop_name);
            $sheet->setCellValue('F' . $row, $user->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'users_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportShopsToExcel($shops)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['ID', 'Name', 'Address', 'Users Count', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        
        // Add data
        $row = 2;
        foreach ($shops as $shop) {
            $sheet->setCellValue('A' . $row, $shop->id);
            $sheet->setCellValue('B' . $row, $shop->name);
            $sheet->setCellValue('C' . $row, $shop->address);
            $sheet->setCellValue('D' . $row, $shop->users->count());
            $sheet->setCellValue('E' . $row, $shop->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'shops_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportModelsToExcel($models)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['ID', 'Model', 'SKU', 'Stars', 'Source', 'First Production', 'Semi/No', 'Average of Stones', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        // Add data
        $row = 2;
        foreach ($models as $model) {
            $sheet->setCellValue('A' . $row, $model->id);
            $sheet->setCellValue('B' . $row, $model->model);
            $sheet->setCellValue('C' . $row, $model->SKU);
            $sheet->setCellValue('D' . $row, $model->stars);
            $sheet->setCellValue('E' . $row, $model->source);
            $sheet->setCellValue('F' . $row, $model->first_production);
            $sheet->setCellValue('G' . $row, $model->semi_or_no);
            $sheet->setCellValue('H' . $row, $model->average_of_stones);
            $sheet->setCellValue('I' . $row, $model->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'models_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportGoldItemsToExcel($goldItems)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['ID', 'Serial Number', 'Model', 'Kind', 'Shop', 'Weight', 'Gold Color', 'Metal Type', 'Metal Purity', 'Quantity', 'Status', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
        
        // Add data
        $row = 2;
        foreach ($goldItems as $item) {
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->serial_number);
            $sheet->setCellValue('C' . $row, $item->model);
            $sheet->setCellValue('D' . $row, $item->kind);
            $sheet->setCellValue('E' . $row, $item->shop_name);
            $sheet->setCellValue('F' . $row, $item->weight);
            $sheet->setCellValue('G' . $row, $item->gold_color);
            $sheet->setCellValue('H' . $row, $item->metal_type);
            $sheet->setCellValue('I' . $row, $item->metal_purity);
            $sheet->setCellValue('J' . $row, $item->quantity);
            $sheet->setCellValue('K' . $row, $item->status);
            $sheet->setCellValue('L' . $row, $item->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'gold_items_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportSoldItemsToExcel($soldItems)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['ID', 'Serial Number', 'Model', 'Shop', 'Customer Name', 'Customer Phone', 'Price', 'Weight', 'Payment Method', 'Sold Date'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        
        // Add data
        $row = 2;
        foreach ($soldItems as $item) {
            $customerName = '';
            $customerPhone = '';
            if ($item->customer) {
                $customerName = $item->customer->first_name . ' ' . $item->customer->last_name;
                $customerPhone = $item->customer->phone_number;
            }
            
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->serial_number);
            $sheet->setCellValue('C' . $row, $item->model);
            $sheet->setCellValue('D' . $row, $item->shop_name);
            $sheet->setCellValue('E' . $row, $customerName);
            $sheet->setCellValue('F' . $row, $customerPhone);
            $sheet->setCellValue('G' . $row, $item->price);
            $sheet->setCellValue('H' . $row, $item->weight);
            $sheet->setCellValue('I' . $row, $item->payment_method);
            $sheet->setCellValue('J' . $row, $item->sold_date );
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'sold_items_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function handleRequestsExport($exportType)
    {
        switch($exportType) {
            case 'add_requests':
                $query = AddRequest::query();
                $this->applyRequestFilters($query);
                return $this->exportAddRequestsToExcel($query->get());
            
            case 'transfer_requests':
                $query = TransferRequest::with(['goldItem', 'fromShop', 'toShop']);
                $this->applyRequestFilters($query);
                return $this->exportTransferRequestsToExcel($query->get());
            
            case 'sale_requests':
                $query = SaleRequest::with(['goldItem', 'customer']);
                $this->applySaleRequestFilters($query);
                return $this->exportSaleRequestsToExcel($query->get());
            
            case 'pound_requests':
                $query = PoundRequest::with(['goldPound', 'shop']);
                $this->applyRequestFilters($query);
                return $this->exportPoundRequestsToExcel($query->get());
            
            case 'transfer_history':
                $query = \App\Models\TransferRequestHistory::with(['fromShop', 'toShop', 'modelDetails']);
                $this->applyTransferHistoryFilters($query);
                return $this->exportTransferHistoryToExcel($query->get());
            
            default:
                return back()->withErrors('Invalid export type');
        }
    }
    
    private function applyRequestFilters($query)
    {
        if (request('search')) {
            $query->where(function($q) {
                $q->where('serial_number', 'like', '%' . request('search') . '%')
                  ->orWhere('model', 'like', '%' . request('search') . '%');
            });
        }
        if (request('shop')) {
            $query->where('shop_name', request('shop'));
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }
    }
    
    private function applySaleRequestFilters($query)
    {
        if (request('search')) {
            $query->where(function($q) {
                $q->where('item_serial_number', 'like', '%' . request('search') . '%')
                  ->orWhereHas('customer', function($customerQuery) {
                      $customerQuery->where('first_name', 'like', '%' . request('search') . '%')
                                   ->orWhere('last_name', 'like', '%' . request('search') . '%');
                  });
            });
        }
        if (request('shop')) {
            $query->where('shop_name', request('shop'));
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }
    }
    
    private function applyTransferHistoryFilters($query)
    {
        if (request('search')) {
            $query->where(function($q) {
                $q->where('serial_number', 'like', '%' . request('search') . '%')
                  ->orWhere('model', 'like', '%' . request('search') . '%');
            });
        }
        if (request('shop')) {
            $query->where(function($q) {
                $q->where('from_shop_name', request('shop'))
                  ->orWhere('to_shop_name', request('shop'));
            });
        }
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }
    }
    
    private function exportAddRequestsToExcel($requests)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['ID', 'Serial Number', 'Model', 'Shop', 'Kind', 'Weight', 'Status', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($requests as $request) {
            $sheet->setCellValue('A' . $row, $request->id);
            $sheet->setCellValue('B' . $row, $request->serial_number);
            $sheet->setCellValue('C' . $row, $request->model);
            $sheet->setCellValue('D' . $row, $request->shop_name);
            $sheet->setCellValue('E' . $row, $request->kind);
            $sheet->setCellValue('F' . $row, $request->weight);
            $sheet->setCellValue('G' . $row, $request->status);
            $sheet->setCellValue('H' . $row, $request->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'add_requests_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportTransferRequestsToExcel($requests)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['ID', 'Item Serial', 'Model', 'From Shop', 'To Shop', 'Type', 'Status', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($requests as $request) {
            $sheet->setCellValue('A' . $row, $request->id);
            $sheet->setCellValue('B' . $row, $request->goldItem ? $request->goldItem->serial_number : 'Pound Transfer');
            $sheet->setCellValue('C' . $row, $request->goldItem ? $request->goldItem->model : '');
            $sheet->setCellValue('D' . $row, $request->from_shop_name);
            $sheet->setCellValue('E' . $row, $request->to_shop_name);
            $sheet->setCellValue('F' . $row, $request->type);
            $sheet->setCellValue('G' . $row, $request->status);
            $sheet->setCellValue('H' . $row, $request->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'transfer_requests_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportSaleRequestsToExcel($requests)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['ID', 'Item Serial', 'Shop', 'Customer Name', 'Customer Phone', 'Price', 'Payment Method', 'Status', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($requests as $request) {
            $customerName = '';
            $customerPhone = '';
            if ($request->customer) {
                $customerName = $request->customer->first_name . ' ' . $request->customer->last_name;
                $customerPhone = $request->customer->phone_number;
            } else {
                $customerName = $request->customer_first_name . ' ' . $request->customer_last_name;
                $customerPhone = $request->customer_phone;
            }
            
            $sheet->setCellValue('A' . $row, $request->id);
            $sheet->setCellValue('B' . $row, $request->item_serial_number);
            $sheet->setCellValue('C' . $row, $request->shop_name);
            $sheet->setCellValue('D' . $row, $customerName);
            $sheet->setCellValue('E' . $row, $customerPhone);
            $sheet->setCellValue('F' . $row, $request->price);
            $sheet->setCellValue('G' . $row, $request->payment_method);
            $sheet->setCellValue('H' . $row, $request->status);
            $sheet->setCellValue('I' . $row, $request->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'sale_requests_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportPoundRequestsToExcel($requests)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['ID', 'Serial Number', 'Shop', 'Type', 'Weight', 'Quantity', 'Status', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($requests as $request) {
            $sheet->setCellValue('A' . $row, $request->id);
            $sheet->setCellValue('B' . $row, $request->serial_number);
            $sheet->setCellValue('C' . $row, $request->shop_name);
            $sheet->setCellValue('D' . $row, $request->type);
            $sheet->setCellValue('E' . $row, $request->weight);
            $sheet->setCellValue('F' . $row, $request->quantity);
            $sheet->setCellValue('G' . $row, $request->status);
            $sheet->setCellValue('H' . $row, $request->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'pound_requests_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportTransferHistoryToExcel($history)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['ID', 'Serial Number', 'Model', 'Kind', 'From Shop', 'To Shop', 'Weight', 'Status', 'Transfer Date', 'Sold Date'];
        $sheet->fromArray($headers, null, 'A1');
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($history as $item) {
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->serial_number);
            $sheet->setCellValue('C' . $row, $item->model);
            $sheet->setCellValue('D' . $row, $item->kind);
            $sheet->setCellValue('E' . $row, $item->from_shop_name);
            $sheet->setCellValue('F' . $row, $item->to_shop_name);
            $sheet->setCellValue('G' . $row, $item->weight);
            $sheet->setCellValue('H' . $row, $item->status);
            $sheet->setCellValue('I' . $row, $item->transfer_completed_at ? $item->transfer_completed_at->format('Y-m-d H:i:s') : '');
            $sheet->setCellValue('J' . $row, $item->item_sold_at ? $item->item_sold_at->format('Y-m-d H:i:s') : '');
            $row++;
        }
        
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'transfer_history_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function downloadExcel($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Save to output
        $writer->save('php://output');
        exit;
    }
    
    private function exportOrdersToExcel($orders)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['ID', 'Order Number', 'Customer Name', 'Customer Phone', 'Shop', 'Seller', 'Order Details', 'Deposit', 'Rest of Cost', 'Total Cost', 'Order Date', 'Deliver Date', 'Payment Method', 'Status', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);
        
        // Add data
        $row = 2;
        foreach ($orders as $order) {
            $totalCost = $order->deposit + $order->rest_of_cost;
            
            $sheet->setCellValue('A' . $row, $order->id);
            $sheet->setCellValue('B' . $row, $order->order_number);
            $sheet->setCellValue('C' . $row, $order->customer_name);
            $sheet->setCellValue('D' . $row, $order->customer_phone);
            $sheet->setCellValue('E' . $row, $order->shop ? $order->shop->name : 'N/A');
            $sheet->setCellValue('F' . $row, $order->seller_name);
            $sheet->setCellValue('G' . $row, $order->order_details);
            $sheet->setCellValue('H' . $row, $order->deposit);
            $sheet->setCellValue('I' . $row, $order->rest_of_cost);
            $sheet->setCellValue('J' . $row, $totalCost);
            $sheet->setCellValue('K' . $row, $order->order_date );
            $sheet->setCellValue('L' . $row, $order->deliver_date);
            $sheet->setCellValue('M' . $row, $order->payment_method);
            $sheet->setCellValue('N' . $row, $order->status);
            $sheet->setCellValue('O' . $row, $order->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'orders_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    private function exportKasrSalesToExcel($kasrSales)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = ['ID', 'Customer Name', 'Customer Phone', 'Shop', 'Items Count', 'Total Weight', 'Net Weight', 'Offered Price', 'Status', 'Order Date', 'Created At'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        
        // Add data
        $row = 2;
        foreach ($kasrSales as $sale) {
            $sheet->setCellValue('A' . $row, $sale->id);
            $sheet->setCellValue('B' . $row, $sale->customer_name);
            $sheet->setCellValue('C' . $row, $sale->customer_phone);
            $sheet->setCellValue('D' . $row, $sale->shop_name);
            $sheet->setCellValue('E' . $row, $sale->items ? $sale->items->count() : 0);
            $sheet->setCellValue('F' . $row, method_exists($sale, 'getTotalWeight') ? $sale->getTotalWeight() : 0);
            $sheet->setCellValue('G' . $row, method_exists($sale, 'getTotalNetWeight') ? $sale->getTotalNetWeight() : 0);
            $sheet->setCellValue('H' . $row, $sale->offered_price);
            $sheet->setCellValue('I' . $row, $sale->status);
            $sheet->setCellValue('J' . $row, $sale->order_date ? $sale->order_date->format('Y-m-d H:i:s') : '');
            $sheet->setCellValue('K' . $row, $sale->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return $this->downloadExcel($spreadsheet, 'kasr_sales_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
} 