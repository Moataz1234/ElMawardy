<?php

namespace App\Http\Controllers;

use App\Services\ShopifyService;
use Illuminate\Http\Request;

class ShopifyProductController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function index(Request $request)
    {
        // Retrieve the cursor from the request, default is null for the first page
        $cursor = $request->input('cursor', null);
    
        // Fetch products from Shopify
        $products = $this->shopifyService->getProducts($cursor);
    
        // Handle pagination safely
        $nextCursor = $products['data']['products']['pageInfo']['endCursor'] ?? null;
        $hasNextPage = $products['data']['products']['pageInfo']['hasNextPage'] ?? false;
    
        // Pass the data to the view, including additional fields
        return view('shopify.products', [
            'products' => $products['data']['products']['edges'] ?? [],
            'nextCursor' => $nextCursor,
            'hasNextPage' => $hasNextPage
        ]);
    }
    
    
}


