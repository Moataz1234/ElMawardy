<?php

namespace App\Http\Controllers\Admin\Shopify;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ShopifyCheckoutController extends ShopifyController
{
    public function index(Request $request)
    {
        $response = $this->shopifyService->getAbandonedCheckouts();
        
        $abandonedCheckouts = collect(json_decode($response->getBody()->getContents(), true)['checkouts']);
        
        // Sorting logic
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if ($sortDirection == 'desc') {
            $abandonedCheckouts = $abandonedCheckouts->sortBy($sortBy);  // Ascending order
        } else {
            $abandonedCheckouts = $abandonedCheckouts->sortByDesc($sortBy); // Descending order
        }

        $perPage = 15; // Define how many items you want per page
        $currentPage = Paginator::resolveCurrentPage();
        $currentPageItems = $abandonedCheckouts->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $paginatedCheckouts = new Paginator($currentPageItems, $perPage, $currentPage);
        
        return view('Shopify.abandoned_checkouts', [
            'checkouts' => $paginatedCheckouts,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection
        ]);
    }
} 