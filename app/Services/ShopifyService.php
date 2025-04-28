<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ShopifyService
{
    protected Client $client;
    protected $storeName;
    protected $accessToken;

    public function __construct()
    {
        $this->storeName = env('SHOPIFY_STORE_NAME');
        $this->accessToken = env('SHOPIFY_ACCESS_TOKEN');
        $this->client = new Client([
            'base_uri' => "https://elmawardy.myshopify.com/admin/api/2024-10/graphql.json",
            'headers' => [
                'X-Shopify-Access-Token' => $this->accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'verify' =>false
        ]);
    }
    public function updateVariant($variantGid, $price, $weight)
    {
        try {
            // Extract variant ID from GID
            $variantId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantGid);
    
            // Prepare API endpoint URL for updating variant details
            $url = "/admin/api/2024-10/variants/{$variantId}.json";
    
            // Prepare data for update including price and weight
            $data = [
                'variant' => [
                    'id' => (int)$variantId,
                    'price' => number_format($price, 2, '.', ''),   // Ensure the price is formatted correctly
                    'weight' => number_format($weight, 2, '.', ''), // Ensure the weight is formatted correctly
                    'weight_unit' => 'g'  // Specify weight unit (grams)
                ]
            ];
    
            // Make the PUT request to update variant prices and weight
            $response = $this->client->put($url, [
                'json' => $data
            ]);
    
            // Return the response as an array (decoded JSON)
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            // Catch any exceptions and return an error message
            return [
                'success' => false,
                'message' => "Error updating variant prices and weight: " . $e->getMessage()
            ];
        }
    }
      
    // public function updateVariant($variantId, $price, $weight)
    // {
    //     // GraphQL mutation to update variant
    //     $mutation = 'mutation productVariantUpdate($input: ProductVariantInput!) {
    //         productVariantUpdate(input: $input) {
    //             productVariant {
    //                 id
    //                 price
    //                 weight
    //             }
    //         }
    //     }';
    
    //     // Variables for the mutation
    //     $variables = [
    //         'input' => [
    //             'id' => $variantId,  // Ensure the variant ID is correctly passed
    //             'price' => $price,   // Update the price
    //             'weight' => $weight, // Update the weight
    //         ],
    //     ];
    
    //     // Make the request
    //     $response = $this->client->post('', [
    //         'body' => json_encode(['query' => $mutation, 'variables' => $variables])
    //     ]);
    
    //     // Check for errors and log the response
    //     $data = json_decode($response->getBody(), true);
    //     Log::info('Shopify Update Response:', ['response' => $data]);
    
    //     // Return updated product variant or null if no update was made
    //     return $data['data']['productVariantUpdate']['productVariant'] ?? null;
    // }
    

public function getProducts($cursor = null, $productId = null)
{
    // Check if a product ID is provided; if so, fetch only that product
    if ($productId) {
        $query = '{
          product(id: "gid://shopify/Product/' . $productId . '") {
            id
            title
            description
            vendor
            productType
            tags
            createdAt
            updatedAt
            media(first: 10) {
              edges {
                node {
                  mediaContentType
                  alt
                  ... on MediaImage {
                    image {
                      url
                      altText
                    }
                  }
                }
              }
            }
            variants(first: 5) {
              edges {
                node {
                  id
                  title
                  price
                  sku
                  inventoryQuantity
                }
              }
            }
          }
        }';
    } else {
        // Conditionally include the `after` argument if a cursor is provided for pagination
        $afterClause = $cursor ? ', after: "' . $cursor . '"' : '';

        $query = '{
          products(first: 50' . $afterClause . ') {
            edges {
              node {
                id
                title
                description
                handle
                vendor
                productType
                tags
                createdAt
                updatedAt
                media(first: 10) {
                  edges {
                    node {
                      mediaContentType
                      alt
                      ... on MediaImage {
                        image {
                          url
                          altText
                        }
                      }
                    }
                  }
                }
                variants(first: 5) {
                  edges {
                    node {
                      id
                      title
                      price
                      sku
                      inventoryQuantity  # Include inventory quantity
                    }
                  }
                }
              }
              cursor
            }
            pageInfo {
              hasNextPage
              endCursor
            }
          }
        }';
    }

    // Make the request
    $response = $this->client->post('', [
        'body' => json_encode(['query' => $query])
    ]);

    return json_decode($response->getBody(), true);
}

    public function updateProduct($productId, $updatedData)
    {
      try {
        $response = $this->client->put("products/{$productId}.json", [
            'json' => [
                'product' => $updatedData
            ]
        ]);

        return [
            'success' => true,
            'data' => json_decode($response->getBody(), true)
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
    }
    public function updateProductDetails($productId, $newImageUrl, $title, $description, $vendor, $productType, $tags)
    {
        // Initialize the array with product data
        $updatedData = [
            'title' => $title,
            'body_html' => $description,
            'vendor' => $vendor,
            'product_type' => $productType,
            'tags' => $tags
        ];

        // If a new image URL is provided, add the image data
        if ($newImageUrl) {
            $updatedData['images'] = [
                [
                    'src' => $newImageUrl
                ]
            ];
        }

        // Make the update request
        return $this->updateProduct($productId, $updatedData);
    }

    public function uploadImageToShopify($productId, $imagePath)
{
    try {
        $imageUrl = asset($imagePath);  // Get the URL for the image in your local server

        $response = $this->client->post("products/{$productId}/images.json", [
            'json' => [
                'image' => [
                    'src' => $imageUrl  // Shopify requires the image URL to be publicly accessible
                ]
            ]
        ]);

        return [
            'success' => true,
            'data' => json_decode($response->getBody(), true)
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
public function updateVariantPrice($variantGid, $newPrice)
{
    try {
        // Define the REST API endpoint with the latest version
        $variantId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantGid);

        $url = "/admin/api/2024-10/variants/{$variantId}.json";

        // Prepare the data for the request
        $data = [
            'variant' => [
                'id' => $variantId,
                'price' => number_format($newPrice, 2, '.', '') // Ensure proper price formatting
            ]
        ];

        // Send the request to Shopify
        $response = $this->client->put($url, [
            'json' => $data
        ]);

        $responseBody = json_decode($response->getBody(), true);

        // Log the response for debugging
        Log::info('Shopify update response for variant ' . $variantId . ': ' . json_encode($responseBody));

        // Return the result based on the response
        if (isset($responseBody['errors'])) {
            return [
                'success' => false,
                'message' => 'Shopify API error: ' . json_encode($responseBody['errors'])
            ];
        }

        return [
            'success' => true,
            'data' => $responseBody['variant']
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
public function updateProductDraft($productGid)
{
    $productId = preg_replace('/^gid:\/\/shopify\/Product\//', '', $productGid);

    $url = "https://{$this->storeName}.myshopify.com/admin/api/2024-10/products/{$productId}.json";

    try {
        $data = [
            'product' => [
                'id' => $productId,
                'status' => 'draft' // Set the status to draft
            ]
        ];

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json'
        ])->put($url, $data);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        } else {
            Log::error("Failed to update product to draft. Response: " . $response->body());
            return ['success' => false, 'message' => $response->body()];
        }
    } catch (\Exception $e) {
        Log::error("Error updating product draft: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

public function getOrders($tab = 'any')
{
    $query = [
        'limit' => 50 // Adjust the limit as necessary
    ];
    
    // Only add status filter if specifically requesting archived or unfulfilled
    if ($tab == 'archived') {
        $query['status'] = 'closed';
    } else if ($tab == 'unfulfilled') {
        $query['status'] = 'open';
        $query['fulfillment_status'] = 'unfulfilled';
    }
    // For 'any', don't add status filters to get all orders

    $response = $this->client->get('orders.json', [
        'query' => $query
    ]);

    $orders = json_decode($response->getBody()->getContents(), true)['orders'];
    foreach ($orders as &$order) {
      foreach ($order['line_items'] as &$item) {
          $sku = $item['sku'];
          // Fetch product by SKU
          $product = $this->getProductBySku($sku);
          // If the product and image exist, attach the image URL to the order
          if ($product && isset($product['media']['edges'][0]['node']['image']['url'])) {
              $item['image_url'] = $product['media']['edges'][0]['node']['image']['url'];
          }
      }
    }
    return $orders;
}
public function getProductBySku($sku)
{
    $query = '{
        products(first: 1, query: "sku:' . $sku . '") {
            edges {
                node {
                    id
                    title
                    variants(first: 5) {
                        edges {
                            node {
                                id
                                sku
                            }
                        }
                    }
                }
            }
        }
    }';

    $response = $this->client->post('', ['body' => json_encode(['query' => $query])]);
    $data = json_decode($response->getBody(), true);

    // Log the response for debugging
    Log::info("Shopify Product Fetch Response: " . json_encode($data));

    $variant = $data['data']['products']['edges'][0]['node']['variants']['edges'][0]['node'] ?? null;

    return $variant ? ['variantId' => $variant['id']] : null;
}

public function getAbandonedCheckouts()
{
    $query = [
        'status' => 'open', // Fetch open abandoned checkouts
        'limit' => 35       // You can adjust the limit as necessary
    ];

    $response = $this->client->get('checkouts.json', [
        'query' => $query
    ]);

    return $response;
}
public function updateFulfillmentStatus($orderId, $status)
{
    $query = [
        'order' => [
            'id' => $orderId,
            'fulfillment_status' => $status
        ]
    ];

    $response = $this->client->put("/admin/api/2024-10/orders/{$orderId}.json", [
        'json' => $query
    ]);
    Log::info('Fulfillment update response', [
      'status_code' => $response->getStatusCode(),
      'response_body' => $response->getBody()->getContents(),
  ]);
    return $response;
}

public function updatePaymentStatus($orderId, $status)
{
    $query = [
        'order' => [
            'id' => $orderId,
            'financial_status' => $status
        ]
    ];

    $response = $this->client->put("/admin/api/2024-10/orders/{$orderId}.json", [
        'json' => $query
    ]);
    Log::info('Payment update response', [
      'status_code' => $response->getStatusCode(),
      'response_body' => $response->getBody()->getContents(),
  ]);
    return $response;
}

public function getLocations()
{
    $response = $this->client->get('locations.json');
    
    $locations = json_decode($response->getBody()->getContents(), true)['locations'];
    
    return $locations;
}

public function fulfillOrder($orderId)
{
  $query = [
    'fulfillment' => [
        'location_id' => env('SHOPIFY_LOCATION_ID'), // Add your valid location ID here
        'tracking_info' => [
            'number' => null,  // Optional tracking number
            'url' => null,     // Optional tracking URL
            'company' => null  // Optional tracking company
        ],
        'line_items' => [],  // Leave empty or specify which line items to fulfill
    ]
];

$response = $this->client->post("orders/{$orderId}/fulfillments.json", [
    'json' => $query
]);

// Log response for debugging
Log::info('Fulfillment creation response', [
    'status_code' => $response->getStatusCode(),
    'response_body' => $response->getBody()->getContents(),
]);

return $response;
}

/**
 * Get a single order by ID
 * 
 * @param string $orderId
 * @return array
 */

public function getOrder($orderId)
{
    $response = $this->client->get("orders/{$orderId}.json");
    return json_decode($response->getBody()->getContents(), true)['order'];
}

/**
 * Update tracking information for an existing fulfillment
 * 
 * @param string $orderId
 * @param string $fulfillmentId
 * @param array $trackingInfo
 * @return array
 */
public function updateTracking($orderId, $fulfillmentId, array $trackingInfo)
{
    $payload = [
        'fulfillment' => [
            'tracking_number' => $trackingInfo['tracking_number'],
            'tracking_company' => $trackingInfo['tracking_company'],
            'notify_customer' => $trackingInfo['notify_customer'] ?? false
        ]
    ];

    $response = $this->client->put(
        "orders/{$orderId}/fulfillments/{$fulfillmentId}.json",
        ['json' => $payload]
    );

    return json_decode($response->getBody()->getContents(), true);
}
public function updateVariantPrices($variantGid, $price, $compareAtPrice)
{
    try {
        // Extract variant ID from GID
        $variantId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantGid);
        
        // Prepare API endpoint URL
        $url = "/admin/api/2024-10/variants/{$variantId}.json";

        // Prepare data for update
        $data = [
            'variant' => [
                'id' => (int)$variantId,
                'price' => number_format($price, 2, '.', ''),
                'compare_at_price' => number_format($compareAtPrice, 2, '.', '')
            ]
        ];

        // Make PUT request to update variant prices
        $response = $this->client->put($url, [
            'json' => $data
        ]);

        // Decode response body
        $responseBody = json_decode($response->getBody(), true);

        // Check for errors in the response
        if (isset($responseBody['errors'])) {
            return [
                'success' => false,
                'message' => 'Shopify API error: ' . json_encode($responseBody['errors'])
            ];
        }

        return [
            'success' => true,
            'data' => $responseBody['variant']
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => "Error updating variant prices: " . $e->getMessage()
        ];
    }
}
public function addProductToCollection($collectionId, $productId)
{
    try {
        $mutation = 'mutation collectionAdd {
            collectionAddProducts(
                id: "' . $collectionId . '",
                productIds: ["' . $productId . '"]
            ) {
                collection {
                    id
                }
                userErrors {
                    field
                    message
                }
            }
        }';

        $response = $this->client->post('', [
            'body' => json_encode(['query' => $mutation])
        ]);

        $responseBody = json_decode($response->getBody(), true);
        
        if (isset($responseBody['data']['collectionAddProducts']['userErrors']) 
            && !empty($responseBody['data']['collectionAddProducts']['userErrors'])) {
            return [
                'success' => false,
                'message' => $responseBody['data']['collectionAddProducts']['userErrors'][0]['message']
            ];
        }

        return [
            'success' => true,
            'data' => $responseBody['data']['collectionAddProducts']
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
public function makeGraphQLRequest($query)
{
    try {
        $response = $this->client->post('', [
            'body' => json_encode(['query' => $query])
        ]);
        
        return json_decode($response->getBody(), true);
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
// public function updateVariantQuantity($variantGid, $quantity)
// {
//     try {
//         // Extract variant ID from GID
//         $variantId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantGid);
        
//         // First, get the inventory item ID for this variant
//         $response = $this->client->get("/admin/api/2024-10/variants/{$variantId}.json");
//         $variantData = json_decode($response->getBody(), true)['variant'];
//         $inventoryItemId = $variantData['inventory_item_id'];
        
//         if (!$inventoryItemId) {
//             throw new \Exception("Could not find inventory item ID for variant {$variantId}");
//         }
        
//         // Get inventory locations for this inventory item
//         $locationsResponse = $this->client->get("/admin/api/2024-10/inventory_levels.json", [
//             'query' => ['inventory_item_ids' => $inventoryItemId]
//         ]);
        
//         $inventoryLevels = json_decode($locationsResponse->getBody(), true)['inventory_levels'];
        
//         if (empty($inventoryLevels)) {
//             // If no inventory levels exist, we need to create one
//             $locations = $this->getLocations();
//             if (empty($locations)) {
//                 throw new \Exception("No locations found for inventory management");
//             }
            
//             $locationId = $locations[0]['id']; // Use the first location
            
//             // Create a new inventory level
//             $this->client->post("/admin/api/2024-10/inventory_levels/set.json", [
//                 'json' => [
//                     'inventory_item_id' => $inventoryItemId,
//                     'location_id' => $locationId,
//                     'available' => $quantity
//                 ]
//             ]);
            
//             Log::info("Created new inventory level for variant {$variantId} with quantity {$quantity}");
//         } else {
//             // Update existing inventory levels
//             foreach ($inventoryLevels as $level) {
//                 $locationId = $level['location_id'];
                
//                 // Use the inventory_levels/set endpoint to directly set the available quantity
//                 $setResponse = $this->client->post("/admin/api/2024-10/inventory_levels/set.json", [
//                     'json' => [
//                         'inventory_item_id' => $inventoryItemId,
//                         'location_id' => $locationId,
//                         'available' => $quantity
//                     ]
//                 ]);
                
//                 $resultData = json_decode($setResponse->getBody(), true);
//                 Log::info("Updated inventory level for variant {$variantId}, location {$locationId} to quantity {$quantity}", [
//                     'response' => $resultData
//                 ]);
//             }
//         }
        
//         // Get the updated variant data to confirm changes
//         $updatedResponse = $this->client->get("/admin/api/2024-10/variants/{$variantId}.json");
//         $updatedVariant = json_decode($updatedResponse->getBody(), true)['variant'];
        
//         Log::info("Final inventory quantity for variant {$variantId}: {$updatedVariant['inventory_quantity']}");
        
//         return [
//             'success' => true,
//             'data' => $updatedVariant
//         ];
//     } catch (\Exception $e) {
//         Log::error("Error updating inventory quantity: " . $e->getMessage());
//         return [
//             'success' => false,
//             'message' => $e->getMessage()
//         ];
//     }
// }
public function updateGInventoryBulk()
{
    $stats = [
        'processed' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => []
    ];
    
    try {
        // Step 1: Get all inventory locations first
        $locations = $this->getLocations();
        if (empty($locations)) {
            throw new \Exception("No inventory locations found");
        }
        $locationIds = array_column($locations, 'id');
        
        // Step 2: Process products in batches with cursor pagination
        $cursor = null;
        $hasNextPage = true;
        $batchSize = 10; // Smaller batch size to avoid timeouts
        $batchCount = 0;
        
        while ($hasNextPage && $batchCount < 3) { // Limit to 3 batches per request to avoid timeouts
            // Get a batch of products
            $products = $this->getProducts($cursor);
            
            if (!isset($products['data']) || !isset($products['data']['products']['edges'])) {
                $stats['errors'][] = "Failed to retrieve products";
                break;
            }
            
            $productEdges = $products['data']['products']['edges'] ?? [];
            $variantsToUpdate = [];
            
            // Collect all G-SKU variants with zero inventory
            foreach ($productEdges as $product) {
                $variants = $product['node']['variants']['edges'] ?? [];
                
                foreach ($variants as $variant) {
                    $sku = $variant['node']['sku'] ?? '';
                    $variantId = $variant['node']['id'] ?? '';
                    $inventoryQuantity = $variant['node']['inventoryQuantity'] ?? 0;
                    
                    // Only process variants with SKUs starting with 'G' and zero inventory
                    if (strlen($sku) > 0 && strtoupper(substr($sku, 0, 1)) === 'G' && $inventoryQuantity == 0) {
                        // Extract the numeric ID from the GID
                        $numericId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantId);
                        
                        $variantsToUpdate[] = [
                            'id' => $numericId,
                            'sku' => $sku,
                            'gid' => $variantId
                        ];
                    } else {
                        $stats['skipped']++;
                    }
                    
                    $stats['processed']++;
                }
            }
            
            // Update inventory for collected variants with proper rate limiting
            foreach ($variantsToUpdate as $index => $variant) {
                try {
                    // Get inventory item ID for this variant (required for inventory updates)
                    $variantResponse = $this->client->get("/admin/api/2024-10/variants/{$variant['id']}.json");
                    $variantData = json_decode($variantResponse->getBody(), true)['variant'];
                    $inventoryItemId = $variantData['inventory_item_id'] ?? null;
                    
                    if (!$inventoryItemId) {
                        $stats['errors'][] = "Missing inventory item ID for variant {$variant['sku']}";
                        continue;
                    }
                    
                    // Update inventory at each location
                    foreach ($locationIds as $locationId) {
                        // Use set endpoint to update inventory level
                        $response = $this->client->post("/admin/api/2024-10/inventory_levels/set.json", [
                            'json' => [
                                'inventory_item_id' => $inventoryItemId,
                                'location_id' => $locationId,
                                'available' => 1
                            ]
                        ]);
                        
                        $result = json_decode($response->getBody(), true);
                        Log::info("Updated inventory for SKU {$variant['sku']} at location {$locationId}", [
                            'result' => $result
                        ]);
                        
                        // Add delay to respect rate limits (max 2 calls per second)
                        usleep(500000); // 0.5 seconds
                    }
                    
                    $stats['updated']++;
                    
                } catch (\Exception $e) {
                    $stats['errors'][] = "Error updating {$variant['sku']}: " . $e->getMessage();
                    Log::error("Error updating inventory for SKU {$variant['sku']}: " . $e->getMessage());
                    
                    // If we hit a rate limit error, pause for a longer time
                    if (strpos($e->getMessage(), 'Too Many Requests') !== false) {
                        Log::info("Rate limit hit, pausing for 2 seconds");
                        sleep(2);
                    }
                }
            }
            
            // Check if there are more pages
            $hasNextPage = $products['data']['products']['pageInfo']['hasNextPage'] ?? false;
            $cursor = $products['data']['products']['pageInfo']['endCursor'] ?? null;
            $batchCount++;
            
            // Add delay between batches
            sleep(1);
        }
        
        return [
            'success' => true,
            'stats' => $stats,
            'next_cursor' => $cursor,
            'has_more' => $hasNextPage
        ];
        
    } catch (\Exception $e) {
        Log::error("Bulk inventory update failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'stats' => $stats
        ];
    }
}

/**
 * Update inventory for all variants of a specific product
 * 
 * @param string $productId The product ID
 * @param int $quantity The quantity to set for all variants
 * @return array Result of the operation
 */
public function updateAllVariantsInventory($productId, $quantity = 1)
{
    try {
        // Get the product with all its variants
        $product = $this->getProducts(null, $productId);
        
        if (!isset($product['data']['product'])) {
            return [
                'success' => false,
                'message' => 'Product not found.'
            ];
        }
        
        $variants = $product['data']['product']['variants']['edges'] ?? [];
        $locations = $this->getLocations();
        $locationIds = array_column($locations, 'id');
        
        $updatedVariants = 0;
        $errors = [];
        
        foreach ($variants as $variant) {
            $variantId = $variant['node']['id'] ?? '';
            $sku = $variant['node']['sku'] ?? '';
            
            if (!$variantId) {
                continue;
            }
            
            try {
                // Extract the numeric ID from the GID
                $numericId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantId);
                
                // Get inventory item ID
                $variantResponse = $this->client->get("/admin/api/2024-10/variants/{$numericId}.json");
                $variantData = json_decode($variantResponse->getBody(), true)['variant'];
                $inventoryItemId = $variantData['inventory_item_id'] ?? null;
                
                if (!$inventoryItemId) {
                    $errors[] = "Missing inventory item ID for variant {$sku}";
                    continue;
                }
                
                // Update inventory at each location
                foreach ($locationIds as $locationId) {
                    $response = $this->client->post("/admin/api/2024-10/inventory_levels/set.json", [
                        'json' => [
                            'inventory_item_id' => $inventoryItemId,
                            'location_id' => $locationId,
                            'available' => $quantity
                        ]
                    ]);
                    
                    // Add delay to respect rate limits
                    usleep(500000);
                }
                
                $updatedVariants++;
                
            } catch (\Exception $e) {
                $errors[] = "Error updating variant {$sku}: " . $e->getMessage();
                
                // If we hit a rate limit error, pause for a longer time
                if (strpos($e->getMessage(), 'Too Many Requests') !== false) {
                    sleep(2);
                }
            }
        }
        
        return [
            'success' => true,
            'updated_variants' => $updatedVariants,
            'errors' => $errors
        ];
        
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
/**
 * Update inventory for ALL products with zero inventory at a specific location
 * 
 * @param string $locationName Name of the location to update
 * @param int $quantity Quantity to set (default 1)
 * @param string|null $startCursor Optional cursor to start processing from
 * @return array Status information about the update process
 */
public function updateAllZeroInventoryAtLocation($locationName = "Part 13 Cairo company for Prefab Bulidings", $quantity = 1, $startCursor = null)
{
    $stats = [
        'processed' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => []
    ];
    
    try {
        // Step 1: Find the specific location by name
        $locations = $this->getLocations();
        $targetLocation = null;
        
        foreach ($locations as $location) {
            if ($location['name'] === $locationName) {
                $targetLocation = $location;
                break;
            }
        }
        
        if (!$targetLocation) {
            throw new \Exception("Location '{$locationName}' not found");
        }
        
        $locationId = $targetLocation['id'];
        Log::info("Targeting inventory updates for ALL zero inventory products at location: {$locationName} (ID: {$locationId})");
        
        // Step 2: Process products in batches with cursor pagination
        $cursor = $startCursor; // Use provided cursor if available
        $hasNextPage = true;
        $batchSize = 250; // Larger batch size to process more products
        $batchCount = 0;
        $maxBatches = 30; // Process up to 10 batches per request to avoid timeouts
        
        while ($hasNextPage && $batchCount < $maxBatches) {
            // Get a batch of products
            $products = $this->getProducts($cursor);
            
            if (!isset($products['data']) || !isset($products['data']['products']['edges'])) {
                $stats['errors'][] = "Failed to retrieve products";
                break;
            }
            
            $productEdges = $products['data']['products']['edges'] ?? [];
            $processedInBatch = 0;
            
            // Process each product
            foreach ($productEdges as $product) {
                $variants = $product['node']['variants']['edges'] ?? [];
                
                foreach ($variants as $variant) {
                    $sku = $variant['node']['sku'] ?? '';
                    $variantId = $variant['node']['id'] ?? '';
                    $variantTitle = $variant['node']['title'] ?? '';
                    $productTitle = $product['node']['title'] ?? 'Unknown Product';
                    $inventoryQuantity = $variant['node']['inventoryQuantity'] ?? null;
                    
                    $stats['processed']++;
                    $processedInBatch++;
                    
                    // Only process variants with zero inventory, regardless of SKU
                    if ($inventoryQuantity === 0) {
                        try {
                            // Extract the numeric ID from the GID
                            $numericId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantId);
                            
                            // Get variant details to find inventory item ID
                            $variantResponse = $this->client->get("/admin/api/2024-10/variants/{$numericId}.json");
                            $variantData = json_decode($variantResponse->getBody(), true)['variant'];
                            $inventoryItemId = $variantData['inventory_item_id'] ?? null;
                            
                            if (!$inventoryItemId) {
                                $stats['errors'][] = "Missing inventory item ID for variant {$sku} ({$productTitle} - {$variantTitle})";
                                continue;
                            }
                            
                            // Get current inventory at the specific location first
                            $inventoryLevelsResponse = $this->client->get("/admin/api/2024-10/inventory_levels.json", [
                                'query' => [
                                    'inventory_item_ids' => $inventoryItemId,
                                    'location_ids' => $locationId
                                ]
                            ]);
                            
                            $inventoryLevels = json_decode($inventoryLevelsResponse->getBody(), true)['inventory_levels'] ?? [];
                            $currentLevel = !empty($inventoryLevels) ? $inventoryLevels[0]['available'] : 0;
                            
                            // Skip if inventory is already greater than 0
                            if ($currentLevel > 0) {
                                Log::info("Skipping {$productTitle} - {$variantTitle} (SKU: {$sku}) - already has inventory {$currentLevel} at location {$locationName}");
                                $stats['skipped']++;
                                continue;
                            }
                            
                            // First, ensure inventory tracking is enabled
                            $this->client->put("/admin/api/2024-10/variants/{$numericId}.json", [
                                'json' => [
                                    'variant' => [
                                        'id' => $numericId,
                                        'inventory_management' => 'shopify'
                                    ]
                                ]
                            ]);
                            
                            // Allow time for the change to propagate
                            usleep(250000); // 0.25 seconds
                            
                            // Use the inventory_levels/set endpoint to update at the specific location
                            $response = $this->client->post("/admin/api/2024-10/inventory_levels/set.json", [
                                'json' => [
                                    'inventory_item_id' => $inventoryItemId,
                                    'location_id' => $locationId,
                                    'available' => $quantity
                                ]
                            ]);
                            
                            $result = json_decode($response->getBody(), true);
                            Log::info("Updated inventory for {$productTitle} - {$variantTitle} (SKU: {$sku}) at location {$locationName}", [
                                'new_level' => $quantity
                            ]);
                            
                            $stats['updated']++;
                            
                            // Add delay to respect rate limits
                            usleep(500000); // 0.5 seconds
                            
                        } catch (\Exception $e) {
                            $stats['errors'][] = "Error updating {$productTitle} - {$variantTitle} (SKU: {$sku}): " . $e->getMessage();
                            Log::error("Error updating inventory: " . $e->getMessage());
                            
                            // If we hit a rate limit error, pause for a longer time
                            if (strpos($e->getMessage(), 'Too Many Requests') !== false) {
                                Log::info("Rate limit hit, pausing for 2 seconds");
                                sleep(2);
                            }
                        }
                    } else {
                        // Skip if already has inventory
                        $stats['skipped']++;
                    }
                }
                
                // If this batch is getting too large, break to avoid timeouts
                if ($processedInBatch >= $batchSize) {
                    break;
                }
            }
            
            // Check if there are more pages
            $hasNextPage = $products['data']['products']['pageInfo']['hasNextPage'] ?? false;
            $cursor = $products['data']['products']['pageInfo']['endCursor'] ?? null;
            $batchCount++;
            
            Log::info("Completed batch {$batchCount} - Processed: {$stats['processed']}, Updated: {$stats['updated']}, Skipped: {$stats['skipped']}");
            
            // Add a small delay between batches
            sleep(1);
        }
        
        return [
            'success' => true,
            'stats' => $stats,
            'next_cursor' => $cursor,
            'has_more' => $hasNextPage,
            'location' => [
                'id' => $locationId,
                'name' => $locationName
            ]
        ];
        
    } catch (\Exception $e) {
        Log::error("Inventory update at location {$locationName} failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'stats' => $stats
        ];
    }
}
/**
 * Set inventory to zero for specific SKUs across ALL locations
 * 
 * @param array $skuList List of SKUs to set to zero inventory
 * @return array Status information about the update process
 */
public function setZeroInventoryForSkusAllLocations($skuList)
{
    $stats = [
        'processed' => 0,
        'updated' => 0,
        'skipped' => 0,
        'not_found' => 0,
        'errors' => []
    ];
    
    try {
        // Normalize SKUs (trim whitespace, convert to uppercase for case-insensitive matching)
        $normalizedSkus = array_map(function($sku) {
            return strtoupper(trim($sku));
        }, $skuList);
        
        // Get all locations
        $locations = $this->getLocations();
        if (empty($locations)) {
            throw new \Exception("No inventory locations found");
        }
        
        Log::info("Setting zero inventory for " . count($normalizedSkus) . " SKUs across ALL " . count($locations) . " locations");
        
        // Process products in batches
        $cursor = null;
        $hasNextPage = true;
        $batchCount = 0;
        $maxBatches = 50; // Set higher to ensure we can process the whole catalog
        $foundSkus = [];
        
        while ($hasNextPage && $batchCount < $maxBatches && count($foundSkus) < count($normalizedSkus)) {
            // Get a batch of products
            $products = $this->getProducts($cursor);
            
            if (!isset($products['data']) || !isset($products['data']['products']['edges'])) {
                $stats['errors'][] = "Failed to retrieve products";
                break;
            }
            
            $productEdges = $products['data']['products']['edges'] ?? [];
            
            // Process each product
            foreach ($productEdges as $product) {
                $variants = $product['node']['variants']['edges'] ?? [];
                $productTitle = $product['node']['title'] ?? 'Unknown Product';
                $productId = basename($product['node']['id']);
                $foundSkusInProduct = false;
                
                // Check if any variant SKU matches our list
                foreach ($variants as $variant) {
                    $sku = $variant['node']['sku'] ?? '';
                    $normalizedSku = strtoupper(trim($sku));
                    
                    // If this SKU is in our list to update
                    if (in_array($normalizedSku, $normalizedSkus)) {
                        $foundSkusInProduct = true;
                        $foundSkus[] = $normalizedSku;
                        break;
                    }
                }
                
                // If any variant matches, set ALL variants to zero inventory
                if ($foundSkusInProduct) {
                    try {
                        Log::info("Found matching SKU in product: {$productTitle} (ID: {$productId})");
                        
                        // Process all variants for this product
                        foreach ($variants as $variant) {
                            $variantId = $variant['node']['id'] ?? '';
                            $variantTitle = $variant['node']['title'] ?? '';
                            $sku = $variant['node']['sku'] ?? '';
                            
                            $stats['processed']++;
                            
                            try {
                                // Extract the numeric ID from the GID
                                $numericId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantId);
                                
                                // Get variant details to find inventory item ID
                                $variantResponse = $this->client->get("/admin/api/2024-10/variants/{$numericId}.json");
                                $variantData = json_decode($variantResponse->getBody(), true)['variant'];
                                $inventoryItemId = $variantData['inventory_item_id'] ?? null;
                                
                                if (!$inventoryItemId) {
                                    $stats['errors'][] = "Missing inventory item ID for variant {$sku} ({$productTitle} - {$variantTitle})";
                                    continue;
                                }
                                
                                // First, ensure inventory tracking is enabled
                                $this->client->put("/admin/api/2024-10/variants/{$numericId}.json", [
                                    'json' => [
                                        'variant' => [
                                            'id' => $numericId,
                                            'inventory_management' => 'shopify'
                                        ]
                                    ]
                                ]);
                                
                                // Allow time for the change to propagate
                                usleep(250000); // 0.25 seconds
                                
                                // Get inventory levels at all locations for this item
                                $inventoryLevelsResponse = $this->client->get("/admin/api/2024-10/inventory_levels.json", [
                                    'query' => [
                                        'inventory_item_ids' => $inventoryItemId
                                    ]
                                ]);
                                
                                $inventoryLevels = json_decode($inventoryLevelsResponse->getBody(), true)['inventory_levels'] ?? [];
                                
                                // If there are no existing inventory levels, create them at all locations
                                if (empty($inventoryLevels)) {
                                    foreach ($locations as $location) {
                                        $locationId = $location['id'];
                                        
                                        // Use the inventory_levels/set endpoint to set inventory to 0
                                        $this->client->post("/admin/api/2024-10/inventory_levels/set.json", [
                                            'json' => [
                                                'inventory_item_id' => $inventoryItemId,
                                                'location_id' => $locationId,
                                                'available' => 0
                                            ]
                                        ]);
                                        
                                        // Add delay to respect rate limits
                                        usleep(500000); // 0.5 seconds
                                    }
                                } else {
                                    // Update existing inventory levels
                                    foreach ($inventoryLevels as $level) {
                                        $locationId = $level['location_id'];
                                        
                                        // Use the inventory_levels/set endpoint to set inventory to 0
                                        $this->client->post("/admin/api/2024-10/inventory_levels/set.json", [
                                            'json' => [
                                                'inventory_item_id' => $inventoryItemId,
                                                'location_id' => $locationId,
                                                'available' => 0
                                            ]
                                        ]);
                                        
                                        // Add delay to respect rate limits
                                        usleep(500000); // 0.5 seconds
                                    }
                                }
                                
                                Log::info("Set inventory to 0 for {$productTitle} - {$variantTitle} (SKU: {$sku}) across all locations");
                                
                                $stats['updated']++;
                                
                            } catch (\Exception $e) {
                                $stats['errors'][] = "Error updating {$productTitle} - {$variantTitle} (SKU: {$sku}): " . $e->getMessage();
                                Log::error("Error setting zero inventory: " . $e->getMessage());
                                
                                // If we hit a rate limit error, pause for a longer time
                                if (strpos($e->getMessage(), 'Too Many Requests') !== false) {
                                    Log::info("Rate limit hit, pausing for 2 seconds");
                                    sleep(2);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $stats['errors'][] = "Error processing product {$productTitle}: " . $e->getMessage();
                        Log::error("Error processing product: " . $e->getMessage());
                    }
                }
            }
            
            // Check if there are more pages
            $hasNextPage = $products['data']['products']['pageInfo']['hasNextPage'] ?? false;
            $cursor = $products['data']['products']['pageInfo']['endCursor'] ?? null;
            $batchCount++;
            
            Log::info("Completed batch {$batchCount} - Found " . count(array_unique($foundSkus)) . " of " . count($normalizedSkus) . " SKUs");
            
            // Add a small delay between batches
            sleep(1);
        }
        
        // Check for any SKUs that weren't found
        $notFoundSkus = array_diff($normalizedSkus, array_unique($foundSkus));
        $stats['not_found'] = count($notFoundSkus);
        
        if (!empty($notFoundSkus)) {
            Log::warning("Could not find the following SKUs: " . implode(", ", array_slice($notFoundSkus, 0, 20)));
            if (count($notFoundSkus) > 20) {
                Log::warning("... and " . (count($notFoundSkus) - 20) . " more.");
            }
        }
        
        return [
            'success' => true,
            'stats' => $stats,
            'not_found_skus' => $notFoundSkus
        ];
        
    } catch (\Exception $e) {
        Log::error("Setting zero inventory failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'stats' => $stats
        ];
    }
}
public function updateVariantPricesAndWeight($variantGid, $price, $compareAtPrice, $weight)
{
    try {
        // Extract variant ID from GID
        $variantId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantGid);
        // Prepare API endpoint URL for updating variant details
        $url = "/admin/api/2024-10/variants/{$variantId}.json";

        // Prepare data for update including weight
        $data = [
            'variant' => [
                'id' => (int)$variantId,
                'price' => number_format($price, 2, '.', ''),
                'compare_at_price' => number_format($compareAtPrice, 2, '.', ''),
                'weight' => number_format($weight, 2, '.', ''), // Update weight here
                'weight_unit' => 'g' // Specify appropriate weight unit (e.g., kg, g, lb)
            ]
        ];

        // Make PUT request to update variant prices and weight
        return json_decode($this->client->put($url, [
            'json' => $data
        ])->getBody(), true);
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => "Error updating variant prices and weight: " . $e->getMessage()
        ];
    }
}

public function getCustomers($limit = 50, $since_id = null)
{
    try {
        $query = [
            'limit' => $limit
        ];

        if ($since_id) {
            $query['since_id'] = $since_id;
        }

        $response = $this->client->get('customers.json', [
            'query' => $query
        ]);

        $customersData = json_decode($response->getBody()->getContents(), true)['customers'];
        
        $processedCustomers = [];
        foreach ($customersData as $customer) {
            $processedCustomers[] = [
                'id' => $customer['id'] ?? null,
                'email' => $customer['email'] ?? null,
                'email_status' => ($customer['verified_email'] ?? false) ? 'Verified' : 'Unverified',
                'first_name' => $customer['first_name'] ?? '',
                'last_name' => $customer['last_name'] ?? '',
                'full_name' => trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')),
                'phone' => $customer['phone'] ?? 'N/A',
                'total_spent' => number_format($customer['total_spent'] ?? 0, 2),
                'orders_count' => $customer['orders_count'] ?? 0,
                'addresses' => array_map(function($address) {
                    return [
                        'id' => $address['id'] ?? null,
                        'address1' => $address['address1'] ?? '',
                        'city' => $address['city'] ?? '',
                        'province' => $address['province'] ?? '',
                        'country' => $address['country'] ?? '',
                        'zip' => $address['zip'] ?? ''
                    ];
                }, $customer['addresses'] ?? []),
                'tags' => $customer['tags'] ?? '',
                'marketing_consent' => ($customer['accepts_marketing'] ?? false) ? 'Subscribed' : 'Unsubscribed',
                'account_status' => $customer['state'] ?? 'active',
                'created_at' => isset($customer['created_at']) ? 
                    \Carbon\Carbon::parse($customer['created_at'])->format('M d, Y H:i') : null
            ];
        }

        return $processedCustomers;
    } catch (\Exception $e) {
        Log::error('Error fetching Shopify customers: ' . $e->getMessage());
        return [];
    }
}

public function getCustomerCount()
{
    try {
        $response = $this->client->get('customers/count.json');
        $count = json_decode($response->getBody()->getContents(), true)['count'];
        return $count;
    } catch (\Exception $e) {
        Log::error('Error fetching Shopify customer count: ' . $e->getMessage());
        return 0;
    }
}

public function searchCustomers($query, $limit = 50)
{
    try {
        $searchParams = [
            'limit' => $limit,
            'query' => $query
        ];

        $response = $this->client->get('customers.json', [
            'query' => $searchParams
        ]);

        $customers = json_decode($response->getBody()->getContents(), true)['customers'];
        
        $processedCustomers = array_map(function($customer) {
            return [
                'id' => $customer['id'],
                'email' => $customer['email'],
                'name' => trim($customer['first_name'] . ' ' . $customer['last_name']),
                'total_spent' => $customer['total_spent'] ?? 0,
                'orders_count' => $customer['orders_count'] ?? 0,
                'created_at' => $customer['created_at']
            ];
        }, $customers);

        return $processedCustomers;
    } catch (\Exception $e) {
        Log::error('Error searching Shopify customers: ' . $e->getMessage());
        return [];
    }
}

public function getCustomer($customerId)
{
    try {
        $response = $this->client->get("customers/{$customerId}.json");
        return json_decode($response->getBody()->getContents(), true)['customer'];
    } catch (\Exception $e) {
        Log::error('Error fetching Shopify customer: ' . $e->getMessage());
        return null;
    }
}

public function getAllCustomersWithoutLimit()
{
    try {
        $allCustomers = [];
        $since_id = null;
        $chunk_size = 250; // Maximum allowed by Shopify
        $page_count = 0;
        $max_pages = ceil($this->getCustomerCount() / $chunk_size); // Calculate total pages needed
        
        do {
            $query = ['limit' => $chunk_size];
            if ($since_id) {
                $query['since_id'] = $since_id;
            }

            Log::info("Fetching customers page " . ($page_count + 1) . " of approximately " . $max_pages);

            $response = $this->client->get('customers.json', [
                'query' => $query,
                'timeout' => 30,
                'connect_timeout' => 30
            ]);

            $customersData = json_decode($response->getBody()->getContents(), true)['customers'];
            
            if (empty($customersData)) {
                break;
            }

            foreach ($customersData as $customer) {
                $allCustomers[] = $this->formatCustomerData($customer);
            }

            $since_id = end($customersData)['id'];
            $page_count++;

            // Add logging to track progress
            Log::info("Processed " . count($allCustomers) . " customers so far");

            // Add a small delay between requests to prevent rate limiting
            usleep(500000); // 0.5 second delay

        } while (!empty($customersData) && $page_count < $max_pages);

        Log::info("Finished processing. Total customers retrieved: " . count($allCustomers));
        return $allCustomers;
    } catch (\Exception $e) {
        Log::error('Error fetching all Shopify customers: ' . $e->getMessage());
        return [];
    }
}

public function getCustomersByPageNumber($page, $limit = 50)
{
    try {
        // Validate page number
        if ($page < 1) {
            Log::error("Invalid page number: {$page}");
            return [];
        }

        $allCustomers = [];
        $since_id = null;
        $totalCustomers = $this->getCustomerCount();
        $totalPages = ceil($totalCustomers / $limit);

        // Check if requested page exists
        if ($page > $totalPages) {
            Log::error("Page {$page} exceeds total pages {$totalPages}");
            return [];
        }

        Log::info("Fetching customers for page {$page} of {$totalPages}");

        // Fetch all customers up to the requested page
        $requestsNeeded = ceil(($page * $limit) / 250);
        
        for ($i = 0; $i < $requestsNeeded; $i++) {
            $query = ['limit' => 250]; // Maximum limit per request
            if ($since_id) {
                $query['since_id'] = $since_id;
            }

            $response = $this->client->get('customers.json', [
                'query' => $query,
                'timeout' => 30,
                'connect_timeout' => 30
            ]);

            $customersData = json_decode($response->getBody()->getContents(), true)['customers'];
            
            if (empty($customersData)) {
                break;
            }

            $allCustomers = array_merge($allCustomers, $customersData);
            $since_id = end($customersData)['id'];

            // Add a small delay to prevent rate limiting
            usleep(500000);
        }

        // Calculate the slice for the requested page
        $offset = ($page - 1) * $limit;
        $pageCustomers = array_slice($allCustomers, $offset, $limit);
        
        $formattedCustomers = array_map([$this, 'formatCustomerData'], $pageCustomers);

        Log::info("Retrieved " . count($formattedCustomers) . " customers for page {$page}");
        return $formattedCustomers;

    } catch (\Exception $e) {
        Log::error("Error fetching Shopify customers for page {$page}: " . $e->getMessage());
        return [];
    }
}

private function formatCustomerData($customer)
{
    return [
        'id' => $customer['id'] ?? null,
        'email' => $customer['email'] ?? null,
        'email_status' => ($customer['verified_email'] ?? false) ? 'Verified' : 'Unverified',
        'first_name' => $customer['first_name'] ?? '',
        'last_name' => $customer['last_name'] ?? '',
        'full_name' => trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')),
        'phone' => $customer['phone'] ?? 'N/A',
        'total_spent' => number_format($customer['total_spent'] ?? 0, 2),
        'orders_count' => $customer['orders_count'] ?? 0,
        'addresses' => array_map(function($address) {
            return [
                'id' => $address['id'] ?? null,
                'address1' => $address['address1'] ?? '',
                'city' => $address['city'] ?? '',
                'province' => $address['province'] ?? '',
                'country' => $address['country'] ?? '',
                'zip' => $address['zip'] ?? ''
            ];
        }, $customer['addresses'] ?? []),
        'tags' => $customer['tags'] ?? '',
        'marketing_consent' => ($customer['accepts_marketing'] ?? false) ? 'Subscribed' : 'Unsubscribed',
        'account_status' => $customer['state'] ?? 'active',
        'created_at' => isset($customer['created_at']) ? 
            \Carbon\Carbon::parse($customer['created_at'])->format('M d, Y H:i') : null
    ];
}
}