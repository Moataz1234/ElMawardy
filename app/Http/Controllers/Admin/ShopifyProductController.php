<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShopifyService;
use Illuminate\Http\Request;
use App\Models\GoldItem;
use App\Models\GoldPrice;
use App\Models\GoldItemSold;
use App\Models\Diamond;
use Illuminate\Pagination\Paginator;
use I18N_Arabic;

use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client; 
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
        
        // $latestGoldPrice = GoldPrice::latest()->first();
        // $goldWithWork = $latestGoldPrice ? $latestGoldPrice->gold_with_work : 0;
    
        // foreach ($productEdges as &$productEdge) {
        //     $shopifyModel = $productEdge['node']['variants']['edges'][0]['node']['sku'] ?? null;

        //     if ($shopifyModel) {
        //         if (str_starts_with($shopifyModel, 'G')) {
        //             $transformedShopifyModel = preg_replace('/^G(\d)(\d{4})([A-D]?)$/', '$1-$2-$3', $shopifyModel);
        //             $transformedShopifyModel = rtrim($transformedShopifyModel, '-'); // Remove trailing hyphen if no letter

        //             Log::info('Transformed Shopify Model: ' . $transformedShopifyModel);

        //             $matchingGoldItems = GoldItem::where('model', $transformedShopifyModel)->get();
        //             $matchingGoldItemsCount = $matchingGoldItems->count();

        //             if ($matchingGoldItemsCount > 0) {
        //                 foreach ($matchingGoldItems as $goldItem) {
        //                     $goldItem->website = true;
        //                     $goldItem->save();
        //                     Log::info('Website updated for model: ' . $goldItem->model);
        //                 }
        //             } else {
        //                 Log::warning('No GoldItems found for transformed model: ' . $transformedShopifyModel);
        //                 $shopifyProductId = $productEdge['node']['id'];
        //                 // $this->makeProductDraft($shopifyProductId); 
        //             }
        //             $maxWeightGoldItem = GoldItem::where('model', $transformedShopifyModel)->max('weight');
        //             $source = GoldItem::where('model', $transformedShopifyModel)->value('source');
          
        //             if ($source === 'Production' || $source === 'Returned') {
        //                 $calculatedPrice = ($maxWeightGoldItem ?? 0) * ($goldWithWork ?? 0);
        //             } else {
        //                 $calculatedPrice = ($maxWeightGoldItem ?? 0) * ($shoghlAgnaby ?? 0);
        //             }
        //             $calculatedPrice = number_format($calculatedPrice, 2, '.', '');
        //             $roundedPrice = round($calculatedPrice / 50) * 50;

        //             Log::info('the price is: ' . $roundedPrice);
        //             Log::info('the weight is: ' . $maxWeightGoldItem);

        //             foreach ($productEdge['node']['variants']['edges'] as &$variant) {
        //                 $variant['node']['inventoryQuantity'] = $matchingGoldItemsCount;
        //                 $variant['node']['price'] = $roundedPrice;

        //                 // if ($matchingGoldItemsCount === 0) {
        //                 //     $shopifyProductId = $productEdge['node']['id'];
        //                 //     // $this->makeProductDraft($shopifyProductId);
        //                 //     Log::info("Product ID {$shopifyProductId} is sold out and has been made a draft.");
        //                 // } else {
        //                     if ($roundedPrice > 0) {
        //                         $shopifyVariantId = $variant['node']['id'];
        //                         $response = $this->shopifyService->updateVariantPrice($shopifyVariantId, $roundedPrice);

        //                         if ($response['success']) {
        //                             Log::info("Price updated for variant ID: {$shopifyVariantId}, New Price: {$roundedPrice}");
        //                         } else {
        //                             Log::error("Failed to update price for variant ID: {$shopifyVariantId}, Error: " . $response['message']);
        //                     }
        //                 }
        //             }
        //         } elseif (str_starts_with($shopifyModel, 'D')) {
        //             $transformedShopifyModel = preg_replace('/^D(\d)(\d{4})([A-D]?)$/', '$1-$2-$3', $shopifyModel);
        //             $transformedShopifyModel = rtrim($transformedShopifyModel, '-');

        //             Log::info('Transformed Shopify Model for Diamond: ' . $transformedShopifyModel);

        //             $matchingDiamonds = Diamond::where('model', $transformedShopifyModel)->get();
        //             $matchingDiamondsCount = $matchingDiamonds->count();

        //             if ($matchingDiamondsCount > 0) {
        //                 foreach ($matchingDiamonds as $diamond) {
        //                     $diamond->website = true;
        //                     $diamond->save();
        //                     Log::info('Website updated for diamond model: ' . $diamond->model);
        //                     // $maxCaratDiamond = Diamond::where('model', $transformedShopifyModel)->max('carat');
        //                     $source = Diamond::where('model', $transformedShopifyModel)->value('source');
        //                 }
        //             } else {
        //                 Log::warning('No Diamonds found for transformed model: ' . $transformedShopifyModel);
        //                 $shopifyProductId = $productEdge['node']['id'];
        //                 // $this->makeProductDraft($shopifyProductId);
        //             }

        //             $diamondPriceFactor = 1000; // Example factor, replace with actual logic
        //             $calculatedPrice = ($maxCaratDiamond ?? 0) * $diamondPriceFactor;
        //             $calculatedPrice = number_format($calculatedPrice, 2, '.', '');
        //             $roundedPrice = round($calculatedPrice / 50) * 50;

        //             Log::info('Diamond price is: ' . $roundedPrice);
                
        return view('shopify.products', [
            'products' => is_array($productEdges) ? $productEdges : [],
            'nextCursor' => $nextCursor,
            'hasNextPage' => $hasNextPage
        ]);
    }
    // private function makeProductDraft($shopifyProductId)
    // {
    //     try {
    //         $response = $this->shopifyService->updateProductDraft($shopifyProductId);
    //         if ($response['success']) {
    //             Log::info("Product ID {$shopifyProductId} has been made a draft.");
    //         } else {
    //             Log::error("Failed to make product ID {$shopifyProductId} a draft. Error: " . $response['message']);
    //         }
    //         return $response;
    //     } catch (\Exception $e) {
    //         Log::error("Exception occurred while making product ID {$shopifyProductId} a draft. Error: " . $e->getMessage());
    //         return false;
    //     }
    // }

    
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
public function Order_index(Request $request)
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
        $perPage = 15; // Define how many orders you want per page
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
public function AbandonedCheckouts_index(Request $request)
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
public function updateSpecificProducts()
{
    $skuData = [
        'D10506B' => ['price' => 182050, 'compare_at_price' => 214150],
        'D10415C' => ['price' => 203450, 'compare_at_price' => 239300],
        'D50893' => ['price' => 1043450, 'compare_at_price' => 1227550],
        'D10136A' => ['price' => 343600, 'compare_at_price' => 404200],
        'D10062A' => ['price' => 42150, 'compare_at_price' => 49550],
        'D10012A' => ['price' => 14450, 'compare_at_price' => 16950],
        'D20579' => ['price' => 72850, 'compare_at_price' => 85650],
        'D20586' => ['price' => 277450, 'compare_at_price' => 326400],
        'D20584' => ['price' => 99800, 'compare_at_price' => 117400],
        'D20583' => ['price' => 92650, 'compare_at_price' => 108950],
        'D20577' => ['price' => 100150, 'compare_at_price' => 117800],
        'D20573' => ['price' => 100750, 'compare_at_price' => 118500],
        'D20569' => ['price' => 171450, 'compare_at_price' => 201700],
        'D20567' => ['price' => 106200, 'compare_at_price' => 124900],
        'D20522B' => ['price' => 110050, 'compare_at_price' => 129450],
        'D20482' => ['price' => 154450, 'compare_at_price' => 181700],
        'D20091' => ['price' => 457250, 'compare_at_price' => 537900],
        'D11054' => ['price' => 46850, 'compare_at_price' => 55100],
        'D72637' => ['price' => 44150, 'compare_at_price' => 51900],
        'D72633' => ['price' => 39400, 'compare_at_price' => 46300],
        'D20562' => ['price' => 136100, 'compare_at_price' => 160100],
        'D20555' => ['price' => 52000, 'compare_at_price' => 61150],
        'D50873' => ['price' => 89000, 'compare_at_price' => 104700],
        'D41046' => ['price' => 72300, 'compare_at_price' => 85050],
        'D40915B' => ['price' => 62200, 'compare_at_price' => 73150],
        'D40773' => ['price' => 163900, 'compare_at_price' => 192800],
        'D72473' => ['price' => 431700, 'compare_at_price' => 507850],
        'D20550' => ['price' => 66300, 'compare_at_price' => 77950],
        'D40968' => ['price' => 51000, 'compare_at_price' => 59950],
        'D41012' => ['price' => 635050, 'compare_at_price' => 747100],
        'D40907B' => ['price' => 75700, 'compare_at_price' => 89050],
        'D41025B' => ['price' => 32900, 'compare_at_price' => 38650],
        'D50822' => ['price' => 62050, 'compare_at_price' => 72950],
        'D20506A' => ['price' => 57100, 'compare_at_price' => 67150]
    ];

    $updatedCount = 0;
    $errors = [];

    $cursor = null;
    do {
        $products = $this->shopifyService->getProducts($cursor);
        
        if (!isset($products['data']['products']['edges'])) {
            continue;
        }

        foreach ($products['data']['products']['edges'] as $productEdge) {
            $variants = $productEdge['node']['variants']['edges'] ?? [];
            
            foreach ($variants as $variant) {
                $sku = $variant['node']['sku'] ?? '';
                
                if (isset($skuData[$sku])) {
                    $variantId = $variant['node']['id'];
                    $price = $skuData[$sku]['price'];
                    $compareAtPrice = $skuData[$sku]['compare_at_price'];
                    
                    try {
                        $response = $this->shopifyService->updateVariantPrices($variantId, $price, $compareAtPrice);
                        
                        if ($response['success']) {
                            $updatedCount++;
                            Log::info("Updated SKU: {$sku} - Price: {$price}, Compare At: {$compareAtPrice}");
                        } else {
                            $errors[] = "Failed to update SKU: {$sku} - " . ($response['message'] ?? 'Unknown error');
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error updating SKU {$sku}: " . $e->getMessage();
                    }
                }
            }
        }
        
        $cursor = $products['data']['products']['pageInfo']['endCursor'] ?? null;
        $hasNextPage = $products['data']['products']['pageInfo']['hasNextPage'] ?? false;
    } while ($hasNextPage && $cursor);

    return response()->json([
        'success' => true,
        'updated_count' => $updatedCount,
        'errors' => $errors
    ]);
}
public function addProductsToCollection()
{
    $skusToAdd = [
        'D10506B', 'D10415C', 'D50893', 'D10136A', 'D10062A', 
        'D10012A', 'D20579', 'D20586', 'D20584', 'D20583', 
        'D20577', 'D20573', 'D20569', 'D20567', 'D20522B', 
        'D20482', 'D20091', 'D11054', 'D72637', 'D72633', 
        'D20562', 'D20555', 'D50873', 'D41046', 'D40915B', 
        'D40773', 'D72473', 'D20550', 'D40968', 'D41012', 
        'D40907B', 'D41025B', 'D50822', 'D20506A'
    ];

    $collectionTitle = "Last In Stock Sale";
    $updatedCount = 0;
    $errors = [];

    try {
        // First, get the collection ID by title
        $query = '{
            collections(first: 1, query: "title:\'' . $collectionTitle . '\'") {
                edges {
                    node {
                        id
                    }
                }
            }
        }';

        $response = $this->shopifyService->makeGraphQLRequest($query);
        
        if (empty($response['data']['collections']['edges'])) {
            return response()->json([
                'success' => false,
                'message' => "Collection '$collectionTitle' not found"
            ]);
        }

        $collectionId = $response['data']['collections']['edges'][0]['node']['id'];

        foreach ($skusToAdd as $sku) {
            try {
                // Find product by SKU
                $query = '{
                    products(first: 1, query: "sku:' . $sku . '") {
                        edges {
                            node {
                                id
                            }
                        }
                    }
                }';

                $response = $this->shopifyService->makeGraphQLRequest($query);
                
                if (!empty($response['data']['products']['edges'])) {
                    $productId = $response['data']['products']['edges'][0]['node']['id'];
                    
                    // Add product to collection
                    $response = $this->shopifyService->addProductToCollection($collectionId, $productId);
                    
                    if ($response['success']) {
                        $updatedCount++;
                        Log::info("Added product with SKU: {$sku} to collection");
                    } else {
                        $errors[] = "Failed to add SKU: {$sku} - " . ($response['message'] ?? 'Unknown error');
                    }
                } else {
                    $errors[] = "Product not found for SKU: {$sku}";
                }
            } catch (\Exception $e) {
                $errors[] = "Error processing SKU {$sku}: " . $e->getMessage();
            }
        }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => "Error finding collection: " . $e->getMessage()
        ]);
    }

    return response()->json([
        'success' => true,
        'updated_count' => $updatedCount,
        'errors' => $errors
    ]);
}
public function updateQuantityToOne()
{
    // List of SKUs to update
    $skusToUpdate = [
        'D10506B', 'D10415C', 'D50893', 'D10136A', 'D10062A', 
        'D10012A', 'D20579', 'D20586', 'D20584', 'D20583', 
        'D20577', 'D20573', 'D20569', 'D20567', 'D20522B', 
        'D20482', 'D20091', 'D11054', 'D72637', 'D72633', 
        'D20562', 'D20555', 'D50873', 'D41046', 'D40915B', 
        'D40773', 'D72473', 'D20550', 'D40968', 'D41012', 
        'D40907B', 'D41025B', 'D50822', 'D20506A'
    ];

    $updatedCount = 0;
    $errors = [];

    $cursor = null;
    do {
        $products = $this->shopifyService->getProducts($cursor);
        
        if (!isset($products['data']['products']['edges'])) {
            continue;
        }

        foreach ($products['data']['products']['edges'] as $productEdge) {
            $variants = $productEdge['node']['variants']['edges'] ?? [];
            
            foreach ($variants as $variant) {
                $sku = $variant['node']['sku'] ?? '';
                
                if (in_array($sku, $skusToUpdate)) {
                    $variantId = $variant['node']['id'];
                    
                    try {
                        $response = $this->shopifyService->updateVariantQuantity($variantId, 1);
                        
                        if ($response['success']) {
                            $updatedCount++;
                            Log::info("Updated quantity for SKU: {$sku} to 1");
                        } else {
                            $errors[] = "Failed to update SKU: {$sku} - " . ($response['message'] ?? 'Unknown error');
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error updating SKU {$sku}: " . $e->getMessage();
                    }
                }
            }
        }
        
        $cursor = $products['data']['products']['pageInfo']['endCursor'] ?? null;
        $hasNextPage = $products['data']['products']['pageInfo']['hasNextPage'] ?? false;
    } while ($hasNextPage && $cursor);

    return response()->json([
        'success' => true,
        'updated_count' => $updatedCount,
        'errors' => $errors
    ]);
}
public function updatePricesFromCsv(Request $request)
{
    $files = $request->file('excel_files'); // Assuming both files are uploaded
    $updatedCount = 0;
    $errors = [];

    foreach ($files as $file) {
        // Read the CSV file
        $data = array_map('str_getcsv', file($file->getRealPath()));
        // Skip header row
        array_shift($data);

        foreach ($data as $row) {
            $sku = trim($row[0]);
            $price = isset($row[1]) ? (float)$row[1] : null;
            $compareAtPrice = isset($row[2]) ? (float)$row[2] : null;

            if ($price !== null && $compareAtPrice !== null) {
                try {
                    // Find product variant by SKU
                    $query = '{
                        products(first: 1, query: "sku:' . $sku . '") {
                            edges {
                                node {
                                    variants(first: 1) {
                                        edges {
                                            node {
                                                id
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }';

                    $response = $this->shopifyService->makeGraphQLRequest($query);
                    
                    if (!empty($response['data']['products']['edges'])) {
                        $variantId = $response['data']['products']['edges'][0]['node']['variants']['edges'][0]['node']['id'];
                        
                        // Update variant prices
                        $updateResponse = $this->shopifyService->updateVariantPrices($variantId, $price, $compareAtPrice);
                        
                        if (isset($updateResponse['success']) && $updateResponse['success']) {
                            Log::info("Updated SKU: {$sku} with Price: {$price} and Compare At Price: {$compareAtPrice}");
                            $updatedCount++;
                        } else {
                            // Handle error case where success is not set or false
                            $errors[] = "Failed to update SKU: {$sku} - " . ($updateResponse['message'] ?? 'Unknown error');
                        }
                    } else {
                        $errors[] = "Product not found for SKU: {$sku}";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error processing SKU {$sku}: " . $e->getMessage();
                }
            } else {
                Log::warning("Invalid data for SKU: {$sku} - Price or Compare At Price is missing.");
            }
        }
    }

    return response()->json([
        'success' => true,
        'updated_count' => $updatedCount,
        'errors' => array_unique($errors)
    ]);
}
    // public function updatePricesFromCsv(Request $request)
    // {
    //     $files = $request->file('excel_files'); // Assuming multiple files can be uploaded
    //     $updatedCount = 0;
    //     $errors = [];

    //     foreach ($files as $file) {
    //         // Read the CSV file
    //         $data = array_map('str_getcsv', file($file->getRealPath()));
    //         array_shift($data); // Skip header row

    //         foreach ($data as $row) {
    //             $sku = trim($row[0]);
    //             $price = isset($row[1]) ? trim($row[1]) : null;
    //             $compareAtPrice = isset($row[2]) ? trim($row[2]) : null;
    //             $weight = isset($row[3]) ? trim($row[3]) : null; // Assuming weight is in column 4

    //             if (empty($price)) {
    //                 // If price is blank, archive SKU (set published status to false)
    //                 try {
    //                     // Find product variant by SKU
    //                     $query = '{
    //                         products(first: 1, query: "sku:' . $sku . '") {
    //                             edges {
    //                                 node {
    //                                     id
    //                                     variants(first: 1) {
    //                                         edges {
    //                                             node {
    //                                                 id
    //                                             }
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }';

    //                     $response = $this->shopifyService->makeGraphQLRequest($query);
                        
    //                     if (!empty($response['data']['products']['edges'])) {
    //                         $productId = $response['data']['products']['edges'][0]['node']['id'];
                            
    //                         // Archive the product by setting its published status to false
    //                         $mutation = 'mutation {
    //                             productUpdate(input: {
    //                                 id: "' . $productId . '",
    //                                 published: false
    //                             }) {
    //                                 userErrors {
    //                                     field
    //                                     message
    //                                 }
    //                             }
    //                         }';

    //                         $updateResponse = $this->shopifyService->makeGraphQLRequest($mutation);
                            
    //                         if (empty($updateResponse['data']['productUpdate']['userErrors'])) {
    //                             Log::info("Archived SKU: {$sku} due to blank price.");
    //                             $updatedCount++;
    //                         } else {
    //                             foreach ($updateResponse['data']['productUpdate']['userErrors'] as $error) {
    //                                 $errors[] = "Failed to archive SKU: {$sku} - " . $error['message'];
    //                             }
    //                         }
    //                     } else {
    //                         $errors[] = "Product not found for SKU: {$sku}";
    //                     }
    //                 } catch (\Exception $e) {
    //                     $errors[] = "Error processing SKU {$sku}: " . $e->getMessage();
    //                 }
    //             } else {
    //                 // Proceed with updating prices and weight if price is provided
    //                 try {
    //                     // Find product variant by SKU
    //                     $query = '{
    //                         products(first: 1, query: "sku:' . $sku . '") {
    //                             edges {
    //                                 node {
    //                                     variants(first: 1) {
    //                                         edges {
    //                                             node {
    //                                                 id
    //                                             }
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }';

    //                     $response = $this->shopifyService->makeGraphQLRequest($query);
                        
    //                     if (!empty($response['data']['products']['edges'])) {
    //                         $variantId = $response['data']['products']['edges'][0]['node']['variants']['edges'][0]['node']['id'];

    //                         // Update variant prices and weight
    //                         $updateResponse = $this->shopifyService->updateVariantPricesAndWeight(
    //                             $variantId,
    //                             (float)$price,
    //                             (float)$compareAtPrice,
    //                             (float)$weight // Pass weight to the service method
    //                         );
                            
    //                         if ($updateResponse['success']) {
    //                             Log::info("Updated SKU: {$sku} with Price: {$price}, Compare At Price: {$compareAtPrice}, Weight: {$weight}");
    //                             $updatedCount++;
    //                         } else {
    //                             $errors[] = "Failed to update SKU: {$sku} - " . ($updateResponse['message'] ?? 'Unknown error');
    //                         }
    //                     } else {
    //                         $errors[] = "Product not found for SKU: {$sku}";
    //                     }
    //                 } catch (\Exception $e) {
    //                     $errors[] = "Error processing SKU {$sku}: " . $e->getMessage();
    //                 }
    //             }
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'updated_count' => $updatedCount,
    //         'errors' => array_unique($errors), // Return unique error messages
    //     ]);
    // }
}






