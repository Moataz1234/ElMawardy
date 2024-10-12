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
    
        // Ensure products is an array
        $productEdges = $products['data']['products']['edges'] ?? [];

        // Pass the data to the view, including additional fields
        return view('shopify.products', [
            'products' => is_array($productEdges) ? $productEdges : [],
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
        $newImageUrl = null;
        if ($request->hasFile('new_image')) {
            $file = $request->file('new_image');
            $newImageUrl = $file->store('images', 'public');
        }
        $title = $request->input('title');
        $description = $request->input('description');
        $vendor = $request->input('vendor');
        $productType = $request->input('product_type');
        $tags = $request->input('tags');

        // Call the Shopify service to update the product details
        $this->shopifyService->updateProductDetails($productId, $imageId, $newImageUrl, $title, $description, $vendor, $productType, $tags);

        return redirect()->route('shopify.products')->with('success', 'Product details updated successfully.');
    }
    public function updateProduct(Request $request, $productId)
    {
        $updatedData = [
            'title' => $request->input('title'),
            'body_html' => $request->input('description'),
            'vendor' => $request->input('vendor'),
            'product_type' => $request->input('product_type'),
            'tags' => $request->input('tags')
        ];

        // Handle image upload if a new image is provided
        if ($request->hasFile('new_image')) {
            $file = $request->file('new_image');
            $newImageUrl = $file->store('images', 'public');
            $updatedData['image'] = ['src' => $newImageUrl];
        }

        // Use ShopifyService to update the product via Shopify API
        $response = $this->shopifyService->updateProduct($productId, $updatedData);

        if ($response['success']) {
            return redirect()->back()->with('success', 'Product updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update product.');
        }
    }

}


