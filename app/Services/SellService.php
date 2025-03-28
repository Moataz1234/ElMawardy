<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\GoldItemSold;
// use App\Models\SoldItemRequest;
use App\Models\GoldItem;
use App\Models\SaleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\GoldPoundInventory;
use Illuminate\Support\Facades\DB;

class SellService
{
    public function getBulkSellFormData(array $ids): array
    {
        $goldItems = GoldItem::with(['poundInventory.goldPound'])
            ->whereIn('id', $ids)
            ->get();

        $associatedPounds = $goldItems->filter(function ($item) {
            return $item->poundInventory !== null;
        })->mapWithKeys(function ($item) {
            return [$item->id => $item->poundInventory];
        });

        return [
            'goldItems' => $goldItems,
            'associatedPounds' => $associatedPounds,
        ];
    }
    public function processBulkSale(array $validatedData): array
    {
        Log::info('Starting bulk sale process', ['data' => $validatedData]);

        return DB::transaction(function () use ($validatedData) {
            $customer = Customer::create(
                [
                    'phone_number' => $validatedData['phone_number'],
                    'email' => $validatedData['email'],
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'address' => $validatedData['address']
                ]
            );

            // Get the sold_date from the form or use null
            $soldDate = isset($validatedData['sold_date']) && !empty($validatedData['sold_date']) 
                ? $validatedData['sold_date'] 
                : null;

            $results = [];
            foreach ($validatedData['ids'] as $id) {
                $goldItem = GoldItem::with('poundInventory.goldPound')->findOrFail($id);
                $poundInventory = $goldItem->poundInventory;

                // Create item sale request
                $itemSaleRequest = SaleRequest::create([
                    'item_serial_number' => $goldItem->serial_number,
                    'shop_name' => Auth::user()->shop_name,
                    'status' => 'pending',
                    'customer_id' => $customer->id,
                    'price' => $validatedData['prices'][$id],
                    'payment_method' => $validatedData['payment_method'],
                    'item_type' => 'item',
                    'weight' => $goldItem->weight,
                    'purity' => $goldItem->metal_purity,
                    'kind' => $goldItem->kind,
                    'sold_date' => $soldDate
                ]);

                $goldItem->update(['status' => 'pending_sale']);
                $results[] = ['item_serial_number' => $goldItem->serial_number, 'sale_id' => $itemSaleRequest->id];

                // If there's an associated pound and price provided
                if ($poundInventory && isset($validatedData['pound_prices'][$poundInventory->serial_number])) {
                    $poundSaleRequest = SaleRequest::create([
                        'item_serial_number' => $poundInventory->serial_number,
                        'shop_name' => Auth::user()->shop_name,
                        'status' => 'pending',
                        'customer_id' => $customer->id,
                        'price' => $validatedData['pound_prices'][$poundInventory->serial_number],
                        'payment_method' => $validatedData['payment_method'],
                        'item_type' => 'pound',
                        'weight' => $poundInventory->weight,
                        'purity' => $poundInventory->purity,
                        'kind' => $poundInventory->goldPound->kind,
                        'related_item_serial' => $goldItem->serial_number,
                        'sold_date' => $soldDate
                    ]);

                    $poundInventory->update(['status' => 'pending_sale']);
                    $results[] = ['pound_serial_number' => $poundInventory->serial_number, 'sale_id' => $poundSaleRequest->id];
                }
            }

            return [
                'success' => true,
                'message' => 'Sale requests created successfully',
                'data' => $results
            ];
        });
    }
    // public function processBulkSale(array $validatedData): array
    // {
    //     Log::info('Starting bulk sale process', ['data' => $validatedData]);

    //     try {
    //         return DB::transaction(function () use ($validatedData) {
    //             // Create the customer
    //             $customer = Customer::create([
    //                 'first_name' => $validatedData['first_name'],
    //                 'last_name' => $validatedData['last_name'],
    //                 'phone_number' => $validatedData['phone_number'],
    //                 'address' => $validatedData['address'],
    //                 'email' => $validatedData['email'],
    //                 'payment_method' => $validatedData['payment_method']
    //             ]);

    //             foreach ($validatedData['ids'] as $id) {
    //                 $goldItem = GoldItem::findOrFail($id);

    //                 // Create main item sale request
    //                 $mainSaleRequest = SaleRequest::create([
    //                     'item_serial_number' => $goldItem->serial_number,
    //                     'shop_name' => Auth::user()->shop_name,
    //                     'status' => 'pending',
    //                     'customer_id' => $customer->id,
    //                     'price' => $validatedData['prices'][$id],
    //                     'payment_method' => $validatedData['payment_method']
    //                 ]);

    //                 // Update item status
    //                 $goldItem->update(['status' => 'pending_sale']);

    //                 // If this item has an associated pound and pound price was provided
    //                 if (isset($validatedData['has_pound']) && 
    //                     $validatedData['has_pound'] === 'true' && 
    //                     isset($validatedData['pound_price']) && 
    //                     isset($validatedData['pound_serial'])) {

    //                     // Get the pound inventory
    //                     $poundInventory = GoldPoundInventory::where('serial_number', $validatedData['pound_serial'])
    //                         ->where('related_item_serial', $goldItem->serial_number)
    //                         ->first();

    //                     if ($poundInventory) {
    //                         $pound = $poundInventory->goldPound;

    //                         // Create sale request for the pound
    //                         SaleRequest::create([
    //                             'item_serial_number' => $poundInventory->serial_number,
    //                             'shop_name' => Auth::user()->shop_name,
    //                             'status' => 'pending',
    //                             'customer_id' => $customer->id,
    //                             'price' => $validatedData['pound_price'],
    //                             'payment_method' => $validatedData['payment_method'],
    //                             'item_type' => 'pound',
    //                             'weight' => $pound->weight,
    //                             'purity' => $pound->purity,
    //                             'kind' => $pound->kind
    //                         ]);

    //                         // Update pound inventory status
    //                         $poundInventory->update(['status' => 'pending_sale']);
    //                     }
    //                 }
    //             }

    //             return [
    //                 'success' => true,
    //                 'message' => 'Sale requests created successfully'
    //             ];
    //         });
    //     } catch (\Exception $e) {
    //         Log::error('Error in bulk sale process', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         throw $e;
    //     }
    // }

    // public function approveSale(SaleRequest $request): void
    // {
    //     $this->approveSingleSale($request);
    //     // $goldItem = GoldItem::where('serial_number', $request->item_serial_number)->first();

    //     if ($request->item_type != 'pound') {
    //         $poundRequest = SaleRequest::where('item_serial_number', $request->item_serial_number)
    //             ->where('item_type', 'pound')
    //             ->first();

    //         if ($poundRequest) {
    //             $this->approveSingleSale($poundRequest);
    //         }
    //     }
    // }
    // private function approveSingleSale(SaleRequest $request): void
    // {
    //     $goldItem = GoldItem::where('serial_number', $request->item_serial_number)->first();

    //     GoldItemSold::create([
    //         ...$goldItem->toArray(),
    //         'customer_id' => $request->customer_id,
    //         'price' => $request->price,
    //         'sold_date' => now(),
    //         'add_date' => $goldItem->rest_since // Add this line

    //     ]);

    //     $goldItem->delete();
    //     $request->update(['status' => 'approved']);
    // }
}
