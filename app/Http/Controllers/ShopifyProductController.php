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
    
    public function showEditImageForm(Request $request)
    {
        $productId = $request->input('product_id');
        $imageId = $request->input('image_id');

        return view('shopify.edit_image', compact('productId', 'imageId'));
    }

    public function editImage(Request $request)
    {
        $productId = $request->input('product_id');
        $imageId = $request->input('image_id');
        $newImageUrl = $request->input('new_image_url');
        $title = $request->input('title');
        $description = $request->input('description');
        $vendor = $request->input('vendor');
        $productType = $request->input('product_type');
        $tags = $request->input('tags');

        // Call the Shopify service to update the product details
        $this->shopifyService->updateProductDetails($productId, $imageId, $newImageUrl, $title, $description, $vendor, $productType, $tags);

        // Call the Shopify service to update the image
        $this->shopifyService->updateProductImage($productId, $imageId, $newImageUrl);

        return redirect()->route('shopify.products')->with('success', 'Image updated successfully.');
    }
}


