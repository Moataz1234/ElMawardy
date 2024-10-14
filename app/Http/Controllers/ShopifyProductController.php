<?php

namespace App\Http\Controllers;

use App\Services\ShopifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    
    public function showEditImageForm(Request $request ,$productId)
    {
        $product = $this->shopifyService->getProducts(null, $productId);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }
    
        // Pass the product data to the view
        return view('shopify.edit_image', ['product' => $product['data']['product']]);
    }

    public function editProduct(Request $request, $productId)
{
    // Validate the form data
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'vendor' => 'required|string|max:255',
        'product_type' => 'nullable|string|max:255',
        'tags' => 'nullable|string',
        'new_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    // Prepare the updated data array only with fields that are not null
    $updatedData = [];

    if ($request->filled('title')) {
        $updatedData['title'] = $request->input('title');
    }

    if ($request->filled('description')) {
        $updatedData['body_html'] = $request->input('description');
    }

    if ($request->filled('vendor')) {
        $updatedData['vendor'] = $request->input('vendor');
    }

    if ($request->filled('product_type')) {
        $updatedData['product_type'] = $request->input('product_type');
    }

    if ($request->filled('tags')) {
        $updatedData['tags'] = explode(',', $request->input('tags'));  // Convert the string back to an array
    }

    // Handle image upload if provided
    if ($request->hasFile('new_image')) {
        $file = $request->file('new_image');
        $filePath = $file->store('images', 'public');  // Store in public storage
        $newImageUrl = Storage::url($filePath);
        $updatedData['images'] = [
            [
                'src' => $newImageUrl
            ]
        ];
    }

    // Call the Shopify service to update the product
    $response = $this->shopifyService->updateProduct($productId, $updatedData);

    if (isset($response['success']) && !$response['success']) {
        return back()->withErrors($response['message'])->withInput();
    }

    return redirect()->route('shopify.products')->with('success', 'Product details updated successfully.');
}
}