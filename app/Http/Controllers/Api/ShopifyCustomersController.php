<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShopifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShopifyCustomersController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    /**
     * Get a list of customers with pagination
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 50);
            $since_id = $request->input('since_id');

            $customers = $this->shopifyService->getCustomers($limit, $since_id);
            $totalCustomers = $this->shopifyService->getCustomerCount();

            return response()->json([
                'success' => true,
                'total_customers' => $totalCustomers,
                'limit' => $limit,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Shopify customers index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search customers by query
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $limit = $request->input('limit', 50);

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required'
                ], 400);
            }

            $customers = $this->shopifyService->searchCustomers($query, $limit);

            return response()->json([
                'success' => true,
                'query' => $query,
                'limit' => $limit,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Shopify customer search: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer details by ID
     */
    public function show($customerId)
    {
        try {
            $customer = $this->shopifyService->getCustomer($customerId);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching Shopify customer details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customer details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllCustomers()
    {
        try {
            $customers = $this->shopifyService->getAllCustomersWithoutLimit();
            $totalCustomers = $this->shopifyService->getCustomerCount();

            return response()->json([
                'success' => true,
                'total_customers' => $totalCustomers,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting all Shopify customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve all customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCustomersByPage($page)
    {
        try {
            $customers = $this->shopifyService->getCustomersByPageNumber($page);
            $totalCustomers = $this->shopifyService->getCustomerCount();

            return response()->json([
                'success' => true,
                'total_customers' => $totalCustomers,
                'current_page' => (int)$page,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting Shopify customers by page: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customers for page ' . $page,
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 