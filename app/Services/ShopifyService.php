<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ShopifyService
{
    protected $client;
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
    public function getProductById($productId)
{
    $query = '{
        product(id: "' . $productId . '") {
            id
            title
            description
            vendor
            productType
            tags
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

    $response = $this->client->post('', [
        'body' => json_encode(['query' => $query])
    ]);

    return json_decode($response->getBody(), true);
}

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
        'limit' => 15 // Adjust the limit as necessary
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
                    media(first: 1) {
                        edges {
                            node {
                                mediaContentType
                                ... on MediaImage {
                                    image {
                                        url
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }';

    $response = $this->client->post('', [
        'body' => json_encode(['query' => $query])
    ]);

    $result = json_decode($response->getBody(), true);
    return $result['data']['products']['edges'][0]['node'] ?? null;
}
public function getAbandonedCheckouts()
{
    $query = [
        'status' => 'open', // Fetch open abandoned checkouts
        'limit' => 15       // You can adjust the limit as necessary
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
}

