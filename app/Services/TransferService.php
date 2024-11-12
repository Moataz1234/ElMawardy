<?php
// app/Services/TransferRequestService.php
namespace App\Services;

use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\User;

use App\Models\TransferRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class TransferService
{
    public function createTransfer(string $itemId, string $toShopName): void
    {
        try {
            $goldItem = GoldItem::findOrFail($itemId);
            $currentUserShop = Auth::user()->shop_name;
    
            TransferRequest::create([
                'gold_item_id' => $goldItem->id,
                'from_shop_name' => $currentUserShop,
                'to_shop_name' => $toShopName,
                'status' => 'pending'
            ]);
        } catch (\Exception $e) {
            Log::error('Transfer Request Creation Failed:', [
                'error' => $e->getMessage(),
                'item_id' => $itemId,
                'to_shop' => $toShopName
            ]);
            throw $e;
        }
    }



    public function handleTransfer(string $requestId, string $status): void
{
    try {
        $transferRequest = TransferRequest::findOrFail($requestId);
        
        if ($status === 'accepted') {
            // Get the shop details from shops table using shop_name
            $toShop = Shop::where('name', $transferRequest->to_shop_name)
                         ->firstOrFail();

            // Only update the gold item's shop information when the transfer is accepted
            $goldItem = $transferRequest->goldItem;
            $goldItem->update([
                'shop_name' => $toShop->name,
                'shop_id' => $toShop->id
            ]);
        }
        
        $transferRequest->update(['status' => $status]);

    } catch (\Exception $e) {
        Log::error('Transfer handling failed:', [
            'error' => $e->getMessage(),
            'request_id' => $requestId,
            'status' => $status
        ]);
        throw $e;
    }
}

    public function getPendingTransfers(): array
    {
        // Get transfers for current user's shop
        $transferRequests = TransferRequest::with('goldItem')
            ->where('to_shop_name', Auth::user()->shop_name)
            ->where('status', 'pending')
            ->get();

        return ['transferRequests' => $transferRequests];
    }

    public function getTransferHistory(): Collection
    {
        return TransferRequest::with(['goldItem', 'fromShop', 'toShop'])->get();
    }

    public function getTransferFormData(string $id): array
    {
        // Get all shops except the current user's shop
        $shops = User::where('usertype', 'user')
                    ->where('shop_name', '!=', Auth::user()->shop_name)
                    ->pluck('shop_name', 'shop_name');

        return [
            'goldItem' => GoldItem::findOrFail($id),
            'shops' => $shops
        ];
    }
    public function transferItem(string $id, array $validated): void
    {
        try {
            $goldItem = GoldItem::findOrFail($id);
            
            // Get the correct shop_id from shops table
            $shop = Shop::where('shop_name', $validated['shop_name'])
                       ->firstOrFail();

            $goldItem->update([
                'shop_name' => $shop->shop_name,
                'shop_id' => $shop->id
            ]);
        } catch (\Exception $e) {
            Log::error('Direct transfer failed:', [
                'error' => $e->getMessage(),
                'item_id' => $id,
                'shop_name' => $validated['shop_name']
            ]);
            throw $e;
        }
    }

    public function getBulkTransferFormData(array $itemIds): array
    {
        // Only get items that don't have pending or accepted transfers
        $goldItems = GoldItem::whereIn('id', $itemIds)
            ->whereNotExists(function ($query) {
                $query->select('id')
                      ->from('transfer_requests')
                      ->whereColumn('transfer_requests.gold_item_id', 'gold_items.id')
                      ->whereIn('status', ['pending', 'accepted']);
            })
            ->get();
    
        return [
            'goldItems' => $goldItems,
            'shops' => Shop::where('name', '!=', Auth::user()->shop_name)
                          ->pluck('name', 'name')
        ];
    }
public function bulkTransfer(array $itemIds, string $toShopName): void
{
    try {
        $toShop = Shop::where('name', $toShopName)->firstOrFail();
        
        // Don't update the shop_name and shop_id immediately
        // Only create transfer requests with pending status
        foreach ($itemIds as $itemId) {
            TransferRequest::create([
                'gold_item_id' => $itemId,
                'from_shop_name' => Auth::user()->shop_name,
                'to_shop_name' => $toShopName,
                'status' => 'pending'
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Bulk transfer failed:', [
            'error' => $e->getMessage(),
            'item_ids' => $itemIds,
            'to_shop' => $toShopName
        ]);
        throw $e;
    }
}
public function clearSelectedItems(array $itemIds): array
{
    Log::info('Cleared selections for items:', ['item_ids' => $itemIds]);
    return [
        'items' => $itemIds,
        'timestamp' => now()->timestamp
    ];
}
}