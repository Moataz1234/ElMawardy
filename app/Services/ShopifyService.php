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
            ]
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

public function getOrders($tab = 'unfulfilled')
{
    $query = [
        'status' => $tab == 'archived' ? 'closed' : 'open', // Retrieve 'closed' orders for archived or 'open' for active/unfulfilled orders
        'limit' => 50 // Adjust the limit as necessary
    ];

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
public function updateVariantQuantity($variantGid, $quantity)
{
    try {
        $variantId = preg_replace('/^gid:\/\/shopify\/ProductVariant\//', '', $variantGid);
        $url = "/admin/api/2024-10/variants/{$variantId}.json";

        $data = [
            'variant' => [
                'id' => $variantId,
                'inventory_quantity' => $quantity,
                'old_inventory_quantity' => $quantity
            ]
        ];

        $response = $this->client->put($url, [
            'json' => $data
        ]);

        $responseBody = json_decode($response->getBody(), true);
        
        Log::info('Shopify update response for variant ' . $variantId . ': ' . json_encode($responseBody));
        
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
}