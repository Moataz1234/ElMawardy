<?php

namespace App\Http\Controllers\Admin\Shopify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShopifyPriceController extends ShopifyController
{
    public function seeUpdatePrice()
    {
        return view('shopify.update_price');
    }
    
    public function updateGoldPrices(Request $request)
    {
        Log::info('Fetching products from Shopify...');

        $pricePerGram = $request->input('price_per_gram');

        if (!$pricePerGram) {
            return redirect()->back()->with('error', 'Please provide a price per gram.');
        }

        // Fetch products with SKUs starting with "G"
        $cursor = null;
        do {
            $products = $this->shopifyService->getProducts($cursor);
            $cursor = $products['data']['products']['pageInfo']['endCursor'] ?? null;
            $productEdges = $products['data']['products']['edges'] ?? [];

            foreach ($productEdges as $edge) {
                $product = $edge['node'];
                $sku = $product['variants']['edges'][0]['node']['sku'] ?? null;

                if ($sku && str_starts_with($sku, 'G')) {
                    Log::info("Processing Gold SKU: {$sku}");

                    foreach ($product['variants']['edges'] as $variantEdge) {
                        $variant = $variantEdge['node'];
                        $weight = $variant['weight'] ?? 0;
                        $weightUnit = $variant['weightUnit'] ?? 'GRAMS';

                        Log::info("Product SKU {$sku} has weight: {$weight} grams");

                        if ($weight > 0) {
                            $newPrice = $weight * $pricePerGram;
                            Log::info("Product SKU {$sku} has weight: {$weight} grams");

                            Log::info("New price for SKU {$sku}: $newPrice (Weight: $weight grams)");

                            // TODO: Uncomment for real API calls
                            // $this->shopifyService->updateVariantPrice($variant['id'], $newPrice);
                        }
                    }
                }
            }
        } while ($products['data']['products']['pageInfo']['hasNextPage'] ?? false);

        return redirect()->back()->with('success', 'Gold prices updated successfully!');
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
    
    public function updateSpecificProducts()
    {
        $skuData = [
            'D10506B' => ['price' => 182050, 'compare_at_price' => 214150],
            'D10415C' => ['price' => 203450, 'compare_at_price' => 239300],
            // ... rest of the SKU data ...
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
            // ... rest of the SKUs ...
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
            // ... rest of the SKUs ...
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
} 