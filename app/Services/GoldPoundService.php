<?php

namespace App\Services;

use App\Models\GoldItemSold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GoldPound;
use App\Models\GoldPoundInventory;
use Illuminate\Support\Facades\DB;
use App\Models\AddRequest;

class GoldPoundService
{
    // Add standalone pounds to shop inventory
    public function addStandalonePounds($shopName, $poundKind, $quantity)
    {
        $goldPound = GoldPound::where('kind', $poundKind)->firstOrFail();

        return GoldPoundInventory::create([
            'gold_pound_id' => $goldPound->id,
            'shop_name' => $shopName,
            'type' => 'standalone',
            'quantity' => $quantity
        ]);
    }

    // Assign pound to gold item
    public function assignPoundToItem($goldItem, $poundKind)
    {
        $goldPound = GoldPound::where('kind', $poundKind)->firstOrFail();

        return GoldPoundInventory::create([
            'gold_pound_id' => $goldPound->id,
            'serial_number' => $goldItem->serial_number,
            'shop_name' => $goldItem->shop_name,
            'type' => 'in_item',
            'quantity' => 1
        ]);
    }

    // Get comprehensive shop report
    public function getShopPoundsReport($shopName)
    {
        $report = [];
        
        $poundTypes = GoldPound::all();
        
        foreach ($poundTypes as $pound) {
            // Count standalone pounds
            $standalone = GoldPoundInventory::where([
                'gold_pound_id' => $pound->id,
                'shop_name' => $shopName,
                'type' => 'standalone'
            ])->sum('quantity');

            // Count pounds in items
            $inItems = GoldPoundInventory::where([
                'gold_pound_id' => $pound->id,
                'shop_name' => $shopName,
                'type' => 'in_item'
            ])->count();

            $report[$pound->kind] = [
                'standalone_quantity' => $standalone,
                'in_items_quantity' => $inItems,
                'total_quantity' => $standalone + $inItems,
                'weight_per_unit' => $pound->weight,
                'total_weight' => ($standalone + $inItems) * $pound->weight
            ];
        }

        return $report;
    }

    // Transfer standalone pounds between shops
    public function transferPounds($fromShop, $toShop, $poundKind, $quantity)
    {
        DB::transaction(function() use ($fromShop, $toShop, $poundKind, $quantity) {
            // Check if source shop has enough pounds
            $sourceInventory = GoldPoundInventory::where([
                'shop_name' => $fromShop,
                'type' => 'standalone'
            ])
            ->whereHas('goldPound', function($query) use ($poundKind) {
                $query->where('kind', $poundKind);
            })
            ->first();

            if (!$sourceInventory || $sourceInventory->quantity < $quantity) {
                throw new \Exception('Insufficient pounds available');
            }

            // Reduce quantity from source shop
            $sourceInventory->quantity -= $quantity;
            $sourceInventory->save();

            // Add to destination shop
            $this->addStandalonePounds($toShop, $poundKind, $quantity);
        });
    }

    public function generateNextSerialNumber()
    {
        $lastSerial = AddRequest::where('model', 'pound')
            ->orderBy('serial_number', 'desc')
            ->value('serial_number');

        if (!$lastSerial) {
            return 'P-0001';
        }

        $number = intval(substr($lastSerial, 2)) + 1;
        return 'P-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
