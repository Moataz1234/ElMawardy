<?php

namespace App\Http\Controllers\Admin\Shopify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShopifyProductController extends ShopifyController
{
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
             
        return view('shopify.products', [
            'products' => is_array($productEdges) ? $productEdges : [],
            'nextCursor' => $nextCursor,
            'hasNextPage' => $hasNextPage
        ]);
    }
    
    public function showEditImageForm(Request $request, $productId)
    {
        $product = $this->shopifyService->getProducts(null, $productId);

        if (!isset($product['data']['product'])) {
            return redirect()->back()->with('error', 'Product not found.');
        }
    
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
    
    public function updateFromExcel(Request $request)
    {
        // Validate and upload file
        $file = $request->file('excel_file');
        if (!$file) {
            return redirect()->back()->with('error', 'Please upload an Excel file.');
        }
    
        // Load the Excel file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    
        // Get the active sheet (first sheet)
        $sheet = $spreadsheet->getActiveSheet();
    
        // Get all rows (data starts from row 2, assuming first row is the header)
        $rows = $sheet->toArray(null, true, true, true);
    
        // Loop through rows and get SKU, Price, and Weight
        foreach ($rows as $row) {
            if (!$row['A']) continue;
        
            $sku = $row['A'];   // SKU in column A
            $price = $row['B']; // Price in column B
            $weight = $row['C']; // Weight in column C
        
            Log::info("Processing SKU: {$sku}, Price: {$price}, Weight: {$weight}");
        
            $shopifyProduct = $this->shopifyService->getProductBySku($sku);
            if (!$shopifyProduct) {
                Log::info("No product found for SKU: {$sku}");
                continue;
            }
        
            $variantId = $shopifyProduct['variantId'] ?? null;
            if ($variantId) {
                Log::info("Updating product with SKU: {$sku}, Variant ID: {$variantId}");
                $response = $this->shopifyService->updateVariant($variantId, $price, $weight);
                Log::info("Shopify Update Response: " . json_encode($response));
            }
        }
    
        return redirect()->back()->with('success', 'Products updated successfully from Excel!');
    }
    public function updateGInventory(Request $request)
    {
        // Increase timeout for this operation
        set_time_limit(600); // 2 minutes
        
        // Get cursor from request if available (for continued processing)
        $cursor = $request->input('cursor');
        
        // Call the bulk update method in ShopifyService
        $result = $this->shopifyService->updateGInventoryBulk();
        
        if (!$result['success']) {
            return redirect()->route('shopify.products')
                ->with('error', 'Inventory update failed: ' . $result['message']);
        }
        
        $stats = $result['stats'];
        $message = "Processed {$stats['processed']} products. ";
        $message .= "Updated {$stats['updated']} variants with zero inventory and SKU starting with G. ";
        $message .= "Skipped {$stats['skipped']} variants (already had inventory or non-G SKU).";
        
        // If there are more products to process, provide a link to continue
        if ($result['has_more']) {
            $nextCursor = $result['next_cursor'];
            $continuationUrl = route('shopify.updateGInventory', ['cursor' => $nextCursor]);
            $message .= " <a href='{$continuationUrl}' class='btn btn-warning'>Continue Processing</a>";
            
            return redirect()->route('shopify.products')
                ->with('warning', $message);
        }
        
        // If any errors occurred, add them to the message
        if (!empty($stats['errors'])) {
            $errorCount = count($stats['errors']);
            $errorSamples = array_slice($stats['errors'], 0, 3);
            $message .= " {$errorCount} errors occurred. Examples: " . implode('; ', $errorSamples);
            
            return redirect()->route('shopify.products')
                ->with('warning', $message);
        }
        
        return redirect()->route('shopify.products')
            ->with('success', $message);
    }
    /**
 * Update inventory for all products with SKUs starting with G at a specific location
 * Only updates products with zero inventory
 */
/**
 * Update inventory for ALL products with zero inventory at the Cairo location
 */
public function updateAllZeroInventory(Request $request)
{
    // Increase timeout for this operation
    set_time_limit(600); // 10 minutes
    
    // Get cursor from request if available (for continued processing)
    $cursor = $request->input('cursor');
    
    // Default location name
    $locationName = "Part 13 Cairo company for Prefab Bulidings";
    
    // Call the targeted update method in ShopifyService
    $result = $this->shopifyService->updateAllZeroInventoryAtLocation($locationName, 1, $cursor);
    
    if (!$result['success']) {
        return redirect()->route('shopify.products')
            ->with('error', 'Inventory update failed: ' . $result['message']);
    }
    
    $stats = $result['stats'];
    $message = "Processed {$stats['processed']} products. ";
    $message .= "Updated {$stats['updated']} variants with zero inventory at location \"{$locationName}\". ";
    $message .= "Skipped {$stats['skipped']} variants (already had inventory).";
    
    // If there are more products to process, provide a link to continue
    if ($result['has_more']) {
        $nextCursor = $result['next_cursor'];
        $continuationUrl = route('shopify.updateAllZeroInventory', ['cursor' => $nextCursor]);
        
        return redirect()->route('shopify.products')
            ->with('warning', $message . " <a href='{$continuationUrl}' class='btn btn-warning'>Continue Processing</a>");
    }
    
    // If any errors occurred, add them to the message
    if (!empty($stats['errors'])) {
        $errorCount = count($stats['errors']);
        $errorSamples = array_slice($stats['errors'], 0, 3);
        $message .= " {$errorCount} errors occurred. Examples: " . implode('; ', $errorSamples);
        
        return redirect()->route('shopify.products')
            ->with('warning', $message);
    }
    
    return redirect()->route('shopify.products')
        ->with('success', $message);
}
/**
 * Import SKUs from Excel and set their inventory to zero
 */
/**
 * Import SKUs from Excel and set their inventory to zero across all locations
 */
public function importSkusSetZero(Request $request)
{
    // Validate the request
    $request->validate([
        'excel_file' => 'required|file|mimes:xlsx,xls,csv'
    ]);
    
    try {
        // Increase timeout for this operation
        set_time_limit(6000); // 5 minutes
        
        // Get the uploaded file
        $file = $request->file('excel_file');
        
        // Load the Excel file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        
        // Get the active sheet
        $sheet = $spreadsheet->getActiveSheet();
        
        // Get all rows
        $rows = $sheet->toArray(null, true, true, true);
        
        // Extract SKUs from the first column (column A)
        $skuList = [];
        foreach ($rows as $row) {
            if (!empty($row['A'])) {
                $skuList[] = $row['A'];
            }
        }
        
        // Remove header row if it exists and looks like a header
        if (count($skuList) > 0 && !is_numeric($skuList[0]) && strtolower($skuList[0]) === 'sku') {
            array_shift($skuList);
        }
        
        if (empty($skuList)) {
            return redirect()->route('shopify.products')
                ->with('error', 'No SKUs found in the uploaded file. Please ensure SKUs are in column A.');
        }
        
        Log::info("Imported " . count($skuList) . " SKUs from Excel file");
        
        // Set inventory to zero for the imported SKUs across all locations
        $result = $this->shopifyService->setZeroInventoryForSkusAllLocations($skuList);
        
        if (!$result['success']) {
            return redirect()->route('shopify.products')
                ->with('error', 'Failed to set inventory: ' . $result['message']);
        }
        
        $stats = $result['stats'];
        $message = "Processed " . count($skuList) . " SKUs from Excel. ";
        $message .= "Updated {$stats['updated']} variants to zero inventory across all locations. ";
        
        if ($stats['not_found'] > 0) {
            $message .= "{$stats['not_found']} SKUs were not found in your store. ";
            
            // Add sample of not found SKUs if available
            if (!empty($result['not_found_skus'])) {
                $sampleSkus = array_slice($result['not_found_skus'], 0, 5);
                $message .= "Examples: " . implode(", ", $sampleSkus);
                
                if (count($result['not_found_skus']) > 5) {
                    $message .= " and " . (count($result['not_found_skus']) - 5) . " more.";
                }
            }
        }
        
        // If there were errors, mention them
        if (!empty($stats['errors'])) {
            $errorCount = count($stats['errors']);
            $message .= " {$errorCount} errors occurred.";
            return redirect()->route('shopify.products')
                ->with('warning', $message);
        }
        
        return redirect()->route('shopify.products')
            ->with('success', $message);
            
    } catch (\Exception $e) {
        Log::error("Excel import failed: " . $e->getMessage());
        return redirect()->route('shopify.products')
            ->with('error', 'Excel import failed: ' . $e->getMessage());
    }
}
} 