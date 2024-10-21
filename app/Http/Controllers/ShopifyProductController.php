<?php

namespace App\Http\Controllers;

use App\Services\ShopifyService;
use Illuminate\Http\Request;
use App\Models\GoldItem;
use App\Models\GoldPrice;
use App\Models\GoldItemSold;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
set_time_limit(600);

class ShopifyProductController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function index(Request $request)
    {
        $cursor = $request->input('cursor', null);
        $products = $this->shopifyService->getProducts($cursor);
        
        if (!isset($products['data'])) {
            return redirect()->back()->with('error', 'Failed to retrieve products from Shopify.');
        }
    
        $nextCursor = $products['data']['products']['pageInfo']['endCursor'] ?? null;
        $hasNextPage = $products['data']['products']['pageInfo']['hasNextPage'] ?? false;
        $productEdges = $products['data']['products']['edges'] ?? [];
        
        $latestGoldPrice = GoldPrice::latest()->first();
        $goldWithWork = $latestGoldPrice ? $latestGoldPrice->gold_with_work : 0;
    
        foreach ($productEdges as &$productEdge) {
            $shopifyModel = $productEdge['node']['variants']['edges'][0]['node']['sku'] ?? null;
    
            if ($shopifyModel) {
                $transformedShopifyModel = preg_replace('/^G(\d{1})(\d{4})$/', '$1-$2', $shopifyModel);
                Log::info('Transformed Shopify Model: ' . $transformedShopifyModel);
    
                $matchingGoldItems = GoldItem::where('model', $transformedShopifyModel)->get();
                $matchingGoldItemsCount = $matchingGoldItems->count();
    
                if ($matchingGoldItemsCount > 0) {
                    foreach ($matchingGoldItems as $goldItem) {
                        $goldItem->website = true;
                        $goldItem->save();
                        Log::info('Website updated for model: ' . $goldItem->model);
                    }
                } else {
                    Log::warning('No GoldItems found for transformed model: ' . $transformedShopifyModel);
                }
    
                // Calculate the maximum weight for the matching GoldItems
                $maxWeightGoldItem = GoldItem::where('model', $transformedShopifyModel)->max('weight');
                $calculatedPrice = ($maxWeightGoldItem ?? 0) * ($goldWithWork ?? 0);
                $calculatedPrice = number_format($calculatedPrice, 2, '.', '');

                foreach ($productEdge['node']['variants']['edges'] as &$variant) {
                    // Update the inventory quantity
                    $variant['node']['inventoryQuantity'] = $matchingGoldItemsCount;

                    // Update the price locally
                    $variant['node']['price'] = $calculatedPrice;

                    // Only update Shopify if the calculated price is greater than 0
                    if ($calculatedPrice > 0) {
                        $shopifyVariantId = $variant['node']['id']; // Get the Shopify variant ID
                        $this->shopifyService->updateVariantPrice($shopifyVariantId, $calculatedPrice); // Call function to update price on Shopify
                    }
                }
            }
        }
        
        return view('shopify.products', [
            'products' => is_array($productEdges) ? $productEdges : [],
            'nextCursor' => $nextCursor,
            'hasNextPage' => $hasNextPage
        ]);
    }
   
    private function updateShopifyProductPrice($variantId, $newPrice)
    {
        $shopName = env('SHOPIFY_STORE_NAME');
        $accessToken = env('SHOPIFY_ACCESS_TOKEN');
        
        $url = "https://{$shopName}.myshopify.com/admin/api/2024-10/variants/gid://shopify/ProductVariant/{$variantId}.json";
    
        $data = [
            'variant' => [
                'id' => $variantId,
                'price' => number_format($newPrice, 2, '.', ''),
            ]
        ];
        Log::info("Attempting to update variant ID {$variantId} with price {$newPrice}.");
        Log::info("Payload: " . json_encode($data));

        $result = $this->shopifyService->updateVariantPrice($variantId, $newPrice);

        if ($result['success']) {
            return response()->json([
                'message' => 'Price updated successfully!',
                'data' => $result['data']
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to update price.',
                'errors' => $result['errors']
            ], 400);
        }
        if ($response->successful()) {
            Log::info("Price updated for variant ID {$variantId} to {$newPrice}.");
        } else {
            Log::error("Failed to update price for variant ID {$variantId}: " . $response->body());
            Log::error("Response status: " . $response->status());
            Log::error("Response data: " . json_encode($response->json()));
            Log::error("Response headers: " . json_encode($response->headers()));

        }
    }
    


    
    public function showEditImageForm(Request $request, $productId)
    {
        $product = $this->shopifyService->getProducts(null, $productId);

        if (!isset($product['data']['product'])) {
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

    $newImageUrl = null;

    // Check if a new image is uploaded
    if ($request->hasFile('new_image')) {
        $file = $request->file('new_image');
        $path = $file->store('images', 'public');
        dd($path); 
        // Upload image to Shopify directly
        $imageUploadResponse = $this->shopifyService->uploadImageToShopify($productId, $path);

        if ($imageUploadResponse['success']) {
            // Get the new image URL from Shopify response
            $newImageUrl = $imageUploadResponse['data']['image']['src'];
        } else {
            return back()->withErrors('Image upload failed: ' . $imageUploadResponse['message'])->withInput();
        }
    }

    // Call the Shopify service to update the product details
    $response = $this->shopifyService->updateProductDetails(
        $productId,
        $newImageUrl,
        $request->input('title'),
        $request->input('description'),
        $request->input('vendor'),
        $request->input('product_type'),
        $request->input('tags')
    );

    if (!$response['success']) {
        return back()->withErrors($response['message'])->withInput();
    }

    return redirect()->route('shopify.products')->with('success', 'Product details updated successfully.');
}

}
