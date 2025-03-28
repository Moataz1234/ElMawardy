<?php

namespace App\Http\Controllers\Admin\Shopify;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Barryvdh\DomPDF\Facade\Pdf;

class ShopifyOrderController extends ShopifyController
{
    public function index(Request $request)
    {
        $currentTab = $request->get('tab', 'unfulfilled');
        $orders = collect($this->shopifyService->getOrders($currentTab));
        
        $sortBy = $request->get('sort_by_' . $currentTab, 'created_at'); // Default sorting by date per tab
        $sortDirection = $request->get('sort_direction_' . $currentTab, 'asc'); // Default direction is descending per tab
        
        if ($currentTab == 'archived') {
            $orders = $orders->where('fulfillment_status', 'fulfilled')
                             ->where('financial_status', '!=', 'voided'); // Exclude voided orders
        } else {
            $orders = $orders->where('fulfillment_status', '!=', 'fulfilled');
        }
        
        if ($sortDirection == 'desc') {
            $orders = $orders->sortBy($sortBy);  // Ascending order
        } else {
            $orders = $orders->sortByDesc($sortBy); // Descending order
        }
        
        $perPage = 25; // Define how many orders you want per page
        $currentPage = Paginator::resolveCurrentPage();
        $currentPageItems = $orders->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $paginatedOrders = new Paginator($currentPageItems, $perPage, $currentPage);
    
        return view('Shopify.orders', [
            'orders' => $paginatedOrders,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'currentTab' => $currentTab  
        ]);
    }
    
    public function fulfillOrder($orderId)
    {
        $response = $this->shopifyService->updateFulfillmentStatus($orderId, 'fulfilled');

        if ($response->getStatusCode() === 200) {
            return redirect()->back()->with('success', 'Order marked as fulfilled.');
        } else {
            return redirect()->back()->with('error', 'Failed to mark order as fulfilled.');
        }
    }

    public function markAsPaid($orderId)
    {
        $response = $this->shopifyService->updatePaymentStatus($orderId, 'paid');

        if ($response->getStatusCode() === 200) {
            return redirect()->back()->with('success', 'Order marked as paid.');
        } else {
            return redirect()->back()->with('error', 'Failed to mark order as paid.');
        }
    }
    
    public function generatePdf($orderId)
    {
        $order = $this->shopifyService->getOrder($orderId);
        
        // Format the data
        $invoiceData = [
            'invoice_number' => $order['name'],
            'invoice_date' => \Carbon\Carbon::parse($order['created_at'])->format('d/m/Y'),
            'customer' => [
                'name' => $order['shipping_address']['name'] ?? 'N/A',
                'address' => [
                    'line1' => $order['shipping_address']['address1'] ?? '',
                    'line2' => $order['shipping_address']['address2'] ?? '',
                    'city' => $order['shipping_address']['city'] ?? '',
                    'country' => $order['shipping_address']['country'] ?? ''
                ]
            ],
            'items' => array_map(function($item) {
                return [
                    'description' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['quantity'] * floatval($item['price'])
                ];
            }, $order['line_items']),
            'total' => $order['total_price'],
            'paid' => $order['total_price'],
            'balance_due' => '0.00',
            'company' => [
                'name' => 'El Mawardy Jewelry',
                'tax_id' => '100-450-296',
                'address' => '7 Soliman Abaza',
                'city' => 'Giza',
                'postal_code' => 'Giza 11211'
            ]
        ];

        $pdf = PDF::loadView('Shopify.invoice', $invoiceData)
            ->setPaper('A4')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
            
        return $pdf->stream('invoice-' . $order['name'] . '.pdf');
    }
} 