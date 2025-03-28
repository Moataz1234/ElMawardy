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
} 