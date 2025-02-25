<?php
// app/Services/TransferRequestService.php
namespace App\Services;

use App\Models\GoldItem;
use App\Models\Shop;
use App\Models\Models;
use App\Models\User;

use App\Models\TransferRequest;
use App\Models\TransferRequestHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class TransferService
{

public function handleTransfer(string $requestId, string $status): void
{
    try {
        Log::info('Handling transfer request:', ['request_id' => $requestId, 'status' => $status]);

        $transferRequest = TransferRequest::findOrFail($requestId);
        Log::info('Transfer request found:', ['transfer_request' => $transferRequest]);

        if ($status === 'accepted') {
            $toShop = Shop::where('name', $transferRequest->to_shop_name)
                         ->firstOrFail();
            Log::info('To shop found:', ['to_shop' => $toShop]);

            $goldItem = $transferRequest->goldItem;
            Log::info('Gold item found:', ['gold_item' => $goldItem]);
            $modelDetails = Models::where('model', $goldItem->model)->first();  
            // Create transfer history record
            TransferRequestHistory::create([
                'from_shop_name' => $transferRequest->from_shop_name,
                'to_shop_name' => $transferRequest->to_shop_name,
                'status' => 'accepted',
                'serial_number' => $goldItem->serial_number,
                'model' => $goldItem->model,
                'kind' => $goldItem->kind,
                'weight' => $goldItem->weight,
                'gold_color' => $goldItem->gold_color,
                'metal_type' => $goldItem->metal_type,
                'metal_purity' => $goldItem->metal_purity,
                'quantity' => $goldItem->quantity,
                'stones' => $goldItem->stones,
                'talab' => $goldItem->talab,
                'transfer_completed_at' => now(),
                'stars' => $modelDetails ? $modelDetails->stars : null,
                'scanned_image' => $modelDetails ? $modelDetails->scanned_image : null
            ]);

            Log::info('Transfer history record created');

            // Update gold item shop
            $goldItem->update([
                'shop_name' => $toShop->name,
                'shop_id' => $toShop->id
            ]);
            Log::info('Gold item updated:', ['gold_item' => $goldItem]);

            // Delete the transfer request
            $transferRequest->delete();
            Log::info('Transfer request deleted');
        } else {
            // If rejected, just update the status
            $transferRequest->update(['status' => $status]);
            Log::info('Transfer request updated:', ['transfer_request' => $transferRequest]);
        }

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
        $shopName = Auth::user()->shop_name;

        // Get incoming transfers
        $incomingRequests = TransferRequest::with('goldItem')
            ->where('to_shop_name', $shopName)
            ->where('status', 'pending')
            ->get();

        // Get outgoing transfers
        $outgoingRequests = TransferRequest::with('goldItem')
            ->where('from_shop_name', $shopName)
            ->get();

        return [
            'incomingRequests' => $incomingRequests,
            'outgoingRequests' => $outgoingRequests
        ];
    }

    public function getTransferHistory(): Collection
    {
        return TransferRequest::with(['goldItem', 'fromShop', 'toShop'])->get();
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
// public function bulkTransfer(array $itemIds, string $toShopName): void
// {
//     try {
//         $toShop = Shop::where('name', $toShopName)->firstOrFail();
        
//         // Don't update the shop_name and shop_id immediately
//         // Only create transfer requests with pending status
//         foreach ($itemIds as $itemId) {
//             TransferRequest::create([
//                 'gold_item_id' => $itemId,
//                 'from_shop_name' => Auth::user()->shop_name,
//                 'to_shop_name' => $toShopName,
//                 'status' => 'pending'
//             ]);
//         }
//     } catch (\Exception $e) {
//         Log::error('Bulk transfer failed:', [
//             'error' => $e->getMessage(),
//             'item_ids' => $itemIds,
//             'to_shop' => $toShopName
//         ]);
//         throw $e;
//     }
// }
public function bulkTransfer(array $itemIds, string $toShopName): void
{
    try {
        $toShop = Shop::where('name', $toShopName)->firstOrFail();
        
        Log::info('Initiating bulk transfer:', [
            'from_shop' => Auth::user()->shop_name,
            'to_shop' => $toShopName,
            'item_ids' => $itemIds
        ]);

        foreach ($itemIds as $itemId) {
            TransferRequest::create([
                'gold_item_id' => $itemId,
                'from_shop_name' => Auth::user()->shop_name,
                'to_shop_name' => $toShopName,
                'status' => 'pending'
            ]);

            Log::info('Transfer request created:', [
                'item_id' => $itemId,
                'to_shop' => $toShopName
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

public function getAllTransferRequests($filters = [])
{
    $query = TransferRequest::with(['goldItem', 'fromShop', 'toShop']);

    // Apply date filter
    if (!empty($filters['date'])) {
        $query->whereDate('created_at', $filters['date']);
    }

    // Apply status filter
    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    // Apply from shop filter
    if (!empty($filters['from_shop'])) {
        $query->where('from_shop_name', $filters['from_shop']);
    }

    // Apply to shop filter
    if (!empty($filters['to_shop'])) {
        $query->where('to_shop_name', $filters['to_shop']);
    }

    // Apply search filter
    if (!empty($filters['search'])) {
        $search = $filters['search'];
        $query->whereHas('goldItem', function ($q) use ($search) {
            $q->where('serial_number', 'LIKE', "%{$search}%")
              ->orWhere('model', 'LIKE', "%{$search}%");
        });
    }

    return $query->orderBy('created_at', 'desc')->get();
}

public function exportToExcel($transferRequests)
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers
    $sheet->setCellValue('A1', 'Serial Number');
    $sheet->setCellValue('B1', 'Model');
    $sheet->setCellValue('C1', 'Weight');
    $sheet->setCellValue('D1', 'From Shop');
    $sheet->setCellValue('E1', 'To Shop');
    $sheet->setCellValue('F1', 'Status');
    $sheet->setCellValue('G1', 'Created At');
    $sheet->setCellValue('H1', 'Updated At');

    // Add data
    $row = 2;
    foreach ($transferRequests as $request) {
        $sheet->setCellValue('A' . $row, $request->goldItem->serial_number);
        $sheet->setCellValue('B' . $row, $request->goldItem->model);
        $sheet->setCellValue('C' . $row, $request->goldItem->weight);
        $sheet->setCellValue('D' . $row, $request->from_shop_name);
        $sheet->setCellValue('E' . $row, $request->to_shop_name);
        $sheet->setCellValue('F' . $row, ucfirst($request->status));
        $sheet->setCellValue('G' . $row, $request->created_at->format('Y-m-d H:i:s'));
        $sheet->setCellValue('H' . $row, $request->updated_at->format('Y-m-d H:i:s'));
        $row++;
    }

    // Auto-size columns
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Create Excel file
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $filename = 'transfer_requests_' . date('Y-m-d_His') . '.xlsx';
    $path = storage_path('app/public/' . $filename);
    $writer->save($path);

    return $filename;
}
}