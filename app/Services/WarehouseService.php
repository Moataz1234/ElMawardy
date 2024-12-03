<?php

namespace App\Services;

use App\Models\GoldItem;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    public function store(array $data)
    {
        return Warehouse::create($data);
    }

    public function findItem($id)
    {
        return Warehouse::findOrFail($id);
    }

    public function update(Warehouse $item, array $data)
    {
        return $item->update($data);
    }

    public function delete($id)
    {
        return Warehouse::findOrFail($id)->delete();
    }

    public function assignToShop(Warehouse $item, $shopId, $shopName)
    {
        return $item->update([
            'shop_id' => $shopId,
            'shop_name' => $shopName
        ]);
    }
    public function transferToWarehouse(GoldItem $goldItem)
    {
        DB::transaction(function () use ($goldItem) {
            // Create warehouse record
            Warehouse::create($goldItem->toArray());
            
            // Delete original item
            $goldItem->delete();
        });
    }
}