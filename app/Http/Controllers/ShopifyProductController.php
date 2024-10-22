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
                if (str_starts_with($shopifyModel, 'G')) {
                    $transformedShopifyModel = preg_replace('/^G(\d)(\d{4})([A-D]?)$/', '$1-$2-$3', $shopifyModel);
                    $transformedShopifyModel = rtrim($transformedShopifyModel, '-'); // Remove trailing hyphen if no letter

                    Log::info('Transformed Shopify Model: ' . $transformedShopifyModel);

                    $matchingGoldItems = GoldItem::where('model', $transformedShopifyModel)->get();
                    $matchingGoldItemsCount = $matchingGoldItems->count();

                    if ($matchingGoldItemsCount > 0) {
                        foreach ($matchingGoldItems as $goldItem) {
                            $goldItem->website = true;
                            $goldItem->save();
                            Log::info('Website updated for model: ' . $goldItem->model);
                            $maxWeightGoldItem = GoldItem::where('model', $transformedShopifyModel)->max('weight');
                            $source = GoldItem::where('model', $transformedShopifyModel)->value('source');
                        }
                    } else {
                        Log::warning('No GoldItems found for transformed model: ' . $transformedShopifyModel);
                        $shopifyProductId = $productEdge['node']['id'];
                        $this->makeProductDraft($shopifyProductId); // Make the product a draft in Shopify
                    }

                    if ($source === 'Production' || $source === 'Returned') {
                        $calculatedPrice = ($maxWeightGoldItem ?? 0) * ($goldWithWork ?? 0);
                    } else {
                        $calculatedPrice = ($maxWeightGoldItem ?? 0) * ($shoghlAgnaby ?? 0);
                    }
                    $calculatedPrice = number_format($calculatedPrice, 2, '.', '');
                    $roundedPrice = round($calculatedPrice / 50) * 50;

                    Log::info('the price is: ' . $roundedPrice);
                    Log::info('the weight is: ' . $maxWeightGoldItem);

                    foreach ($productEdge['node']['variants']['edges'] as &$variant) {
                        $variant['node']['inventoryQuantity'] = $matchingGoldItemsCount;
                        $variant['node']['price'] = $roundedPrice;

                        if ($matchingGoldItemsCount === 0) {
                            $shopifyProductId = $productEdge['node']['id'];
                            $this->makeProductDraft($shopifyProductId);
                            Log::info("Product ID {$shopifyProductId} is sold out and has been made a draft.");
                        } else {
                            if ($roundedPrice > 0) {
                                $shopifyVariantId = $variant['node']['id'];
                                $response = $this->shopifyService->updateVariantPrice($shopifyVariantId, $roundedPrice);

                                if ($response['success']) {
                                    Log::info("Price updated for variant ID: {$shopifyVariantId}, New Price: {$roundedPrice}");
                                } else {
                                    Log::error("Failed to update price for variant ID: {$shopifyVariantId}, Error: " . $response['message']);
                                }
                            }
                        }
                    }
                } elseif (str_starts_with($shopifyModel, 'D')) {
                    $transformedShopifyModel = preg_replace('/^D(\d)(\d{4})([A-D]?)$/', '$1-$2-$3', $shopifyModel);
                    $transformedShopifyModel = rtrim($transformedShopifyModel, '-');

                    Log::info('Transformed Shopify Model for Diamond: ' . $transformedShopifyModel);

                    $matchingDiamonds = Diamond::where('model', $transformedShopifyModel)->get();
                    $matchingDiamondsCount = $matchingDiamonds->count();

                    if ($matchingDiamondsCount > 0) {
                        foreach ($matchingDiamonds as $diamond) {
                            $diamond->website = true;
                            $diamond->save();
                            Log::info('Website updated for diamond model: ' . $diamond->model);
                            $maxCaratDiamond = Diamond::where('model', $transformedShopifyModel)->max('carat');
                            $source = Diamond::where('model', $transformedShopifyModel)->value('source');
                        }
                    } else {
                        Log::warning('No Diamonds found for transformed model: ' . $transformedShopifyModel);
                        $shopifyProductId = $productEdge['node']['id'];
                        $this->makeProductDraft($shopifyProductId);
                    }

                    $diamondPriceFactor = 1000; // Example factor, replace with actual logic
                    $calculatedPrice = ($maxCaratDiamond ?? 0) * $diamondPriceFactor;
                    $calculatedPrice = number_format($calculatedPrice, 2, '.', '');
                    $roundedPrice = round($calculatedPrice / 50) * 50;

                    Log::info('Diamond price is: ' . $roundedPrice);
                    Log::info('Diamond carat is: ' . $maxCaratDiamond);

                    foreach ($productEdge['node']['variants']['edges'] as &$variant) {
                        $variant['node']['inventoryQuantity'] = $matchingDiamondsCount;
                        $variant['node']['price'] = $roundedPrice;

                        if ($matchingDiamondsCount === 0) {
                            $shopifyProductId = $productEdge['node']['id'];
                            $this->makeProductDraft($shopifyProductId);
                            Log::info("Diamond Product ID {$shopifyProductId} is sold out and has been made a draft.");
                        } else {
                            if ($roundedPrice > 0) {
                                $shopifyVariantId = $variant['node']['id'];
                                $response = $this->shopifyService->updateVariantPrice($shopifyVariantId, $roundedPrice);

                                if ($response['success']) {
                                    Log::info("Price updated for diamond variant ID: {$shopifyVariantId}, New Price: {$roundedPrice}");
                                } else {
                                    Log::error("Failed to update price for diamond variant ID: {$shopifyVariantId}, Error: " . $response['message']);
                                }
                            }
                        }
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
    private function makeProductDraft($shopifyProductId)
    {
        try {
            $response = $this->shopifyService->updateProductDraft($shopifyProductId);
            if ($response['success']) {
                Log::info("Product ID {$shopifyProductId} has been made a draft.");
            } else {
                Log::error("Failed to make product ID {$shopifyProductId} a draft. Error: " . $response['message']);
            }
            return $response;
        } catch (\Exception $e) {
            Log::error("Exception occurred while making product ID {$shopifyProductId} a draft. Error: " . $e->getMessage());
            return false;
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
