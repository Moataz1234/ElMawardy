<?php

namespace App\Http\Controllers\Api\Rabea;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;
use App\Services\OrderService;
use App\Http\Requests\OrderUpdateRequest;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RabiaApiController extends Controller
{
    protected $orderService;
    protected $orderRepository;

    public function __construct(OrderService $orderService, OrderRepository $orderRepository)
    {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get filtered orders for Rabea dashboard
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
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
        
            $orders = $this->orderRepository->getFilteredOrders(
                $sortField,
                $sortDirection,
                $searchType,
                $searchValue
            );
        
            return response()->json([
                'status' => 'success',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching orders: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders to be printed (workshop orders)
     */
    public function getToPrintOrders(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderRepository->getToPrintOrders(
                $request->get('filter'),
                $request->get('sort', 'order_date'),
                $request->get('direction', 'desc')
            );

            return response()->json([
                'status' => 'success',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching print orders: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch print orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get completed orders
     */
    public function getCompletedOrders(Request $request): JsonResponse
    {
        try {
            $searchType = $request->input('search_type');
            $searchValue = $request->input('search_value');
            $sortField = $request->input('sort', 'created_at');
            $sortDirection = $request->input('direction', 'asc');
        
            $query = Order::where('status', 'خلص');
        
            if ($searchType && $searchValue) {
                $query->where($searchType, 'LIKE', "%{$searchValue}%");
            }
        
            $allowedSortFields = [
                'order_number',
                'customer_name',
                'seller_name',
                'status',
                'created_at'
            ];
        
            if (in_array($sortField, $allowedSortFields)) {
                $orders = $query->orderBy($sortField, $sortDirection)->paginate(20);
            } else {
                $orders = $query->orderBy('created_at', 'desc')->paginate(20);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching completed orders: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch completed orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status in bulk
     */
    public function updateStatusBulk(Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');
            $orderIds = $request->input('order_ids');

            if (!$orderIds) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No orders selected'
                ], 400);
            }

            Order::whereIn('id', $orderIds)->update(['status' => $status]);

            return response()->json([
                'status' => 'success',
                'message' => 'Orders status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating orders status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update orders status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single order details
     */
    public function getOrder($id): JsonResponse
    {
        try {
            $order = Order::with('items')->findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch order details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update single order
     */
    public function updateOrder(Request $request, $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            // Update order data
            $order->update([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'seller_name' => $request->seller_name,
                'order_details' => $request->order_details,
                'deposit' => $request->deposit,
                'rest_of_cost' => $request->rest_of_cost,
                'payment_method' => $request->payment_method,
                'order_date' => $request->order_date,
            ]);

            // Update order items
            foreach ($request->order_kind as $index => $orderKind) {
                $item = $order->items()->get()[$index];
                $item->update([
                    'order_kind' => $orderKind,
                    'item_type' => $request->item_type[$index],
                    'weight' => $request->weight[$index],
                    'model' => $request->model[$index],
                    'serial_number' => $request->serial_number[$index],
                    'order_details' => $request->order_details[$index],
                    'order_type' => $request->order_type[$index],
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order updated successfully',
                'data' => $order->fresh(['items'])
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 