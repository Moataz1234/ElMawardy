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
            // Check if customer already exists with the given phone number
            $customer = null;
            if (!empty($validatedData['phone_number'])) {
                $customer = Customer::where('phone_number', $validatedData['phone_number'])->first();
            }

            // If customer doesn't exist, create a new one
            if (!$customer) {
                $customer = Customer::create([
                    'phone_number' => $validatedData['phone_number'],
                    'email' => $validatedData['email'],
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'address' => $validatedData['address'],
                    'payment_method' => $validatedData['payment_method']
                ]);
            }

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
}
