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

class SellService
{
    public function getBulkSellFormData(array $ids): array
    {
        return [
            'goldItems' => GoldItem::whereIn('id', $ids)->get()
        ];
    }

    public function processBulkSale(array $validatedData): void
    {
        // Add this line for debugging
        Log::info('Validated Data:', $validatedData);

        // Validate prices
        if (!isset($validatedData['prices']) || count($validatedData['prices']) !== count($validatedData['ids'])) {
            throw new \Exception('Invalid prices provided for the selected items.');
        }

        // Create the customer
        $customer = Customer::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'phone_number' => $validatedData['phone_number'],
            'address' => $validatedData['address'],
            'email' => $validatedData['email'],
            'payment_method' => $validatedData['payment_method']
        ]);

        // Loop through each item and create a sale request
        foreach ($validatedData['ids'] as $id) {
            $goldItem = GoldItem::findOrFail($id);

            // Get the price for this specific item
            $price = $validatedData['prices'][$id];

            // Add this line for debugging
            Log::info('Creating sale request:', [
                'payment_method' => $validatedData['payment_method'],
                'item_serial' => $goldItem->serial_number,
                'price' => $price
            ]);

            // Create the sale request with payment_method
            SaleRequest::create([
                'item_serial_number' => $goldItem->serial_number,
                'shop_name' => Auth::user()->shop_name,
                'status' => 'pending',
                'customer_id' => $customer->id,
                'price' => $price,
                'payment_method' => $validatedData['payment_method']
            ]);
        }
    }

    public function approveSale(SaleRequest $request): void
    {
        $goldItem = GoldItem::where('serial_number', $request->item_serial_number)->first();

        GoldItemSold::create([
            ...$goldItem->toArray(),
            'customer_id' => $request->customer_id,
            'price' => $request->price,
            'sold_date' => now(),
            'add_date' => $goldItem->rest_since // Add this line

        ]);
        $goldItem->delete();
        $request->update(['status' => 'approved']);
    }
}
