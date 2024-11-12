<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\GoldItem;
use App\Models\GoldItemSold;
use Illuminate\Http\Request;

class SellService
{
public function getBulkSellFormData(string $ids): array
{
    return [
        'goldItems' => GoldItem::whereIn('id', explode(',', $ids))->get()
    ];
}

public function processBulkSale(array $validatedData): void
    {
        $customer = Customer::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'phone_number' => $validatedData['phone_number'],
            'address' => $validatedData['address'],
            'email' => $validatedData['email'],
            'payment_method' => $validatedData['payment_method']
        ]);

        foreach ($validatedData['ids'] as $id) {
            $goldItem = GoldItem::findOrFail($id);
            
            GoldItemSold::create([
                ...$goldItem->toArray(),
                'customer_id' => $customer->id,
                'sold_date' => now()
            ]);

            $goldItem->delete();
        }
    }
}