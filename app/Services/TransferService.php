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
use Illuminate\Support\Facades\DB;

class TransferService
{

public function handleTransfer($requestId, $status)
{
    return DB::transaction(function () use ($requestId, $status) {
        // Load both relationships explicitly
        $request = TransferRequest::with(['goldItem', 'pound.goldPound'])
            ->findOrFail($requestId);
        
        Log::info('Processing transfer request', [
            'request_id' => $requestId,
            'type' => $request->type,
            'status' => $status,
            'pound_id' => $request->pound_id,
            'item_id' => $request->gold_item_id
        ]);

        if ($status === 'accepted') {
            // Get the destination shop's ID
            $toShop = Shop::where('name', $request->to_shop_name)->first();
            
            if (!$toShop) {
                Log::error('Destination shop not found', [
                    'shop_name' => $request->to_shop_name
                ]);
                throw new \Exception('Destination shop not found');
            }

            if ($request->type == 'pound') {
                $pound = $request->pound;
                if ($pound) {
                    Log::info('Updating pound location', [
                        'pound_id' => $pound->id,
                        'new_shop' => $request->to_shop_name,
                        'new_shop_id' => $toShop->id
                    ]);
                    
                    $pound->update([
                        'shop_name' => $request->to_shop_name,
                        'shop_id' => $toShop->id,
                        'status' => 'active'
                    ]);
                    
                    // Create history record for pound transfer
                    TransferRequestHistory::create([
                        'from_shop_name' => $request->from_shop_name,
                        'to_shop_name' => $request->to_shop_name,
                        'status' => 'completed',
                        'serial_number' => $pound->serial_number,
                        'kind' => $pound->goldPound->kind ?? null,
                        'weight' => $pound->goldPound->weight ?? null,
                        'metal_purity' => $pound->purity ?? null,
                        'quantity' => $pound->quantity ?? 1,
                        'transfer_completed_at' => now()
                    ]);
                } else {
                    Log::error('Pound not found for transfer request', [
                        'request_id' => $requestId,
                        'pound_id' => $request->pound_id
                    ]);
                }
            } else {
                if ($request->goldItem) {
                    $request->goldItem->update([
                        'shop_name' => $request->to_shop_name,
                        'shop_id' => $toShop->id,
                        'status' => 'available'
                    ]);
                    
                    // Create history record for gold item transfer
                    TransferRequestHistory::create([
                        'from_shop_name' => $request->from_shop_name,
                        'to_shop_name' => $request->to_shop_name,
                        'status' => 'completed',
                        'serial_number' => $request->goldItem->serial_number,
                        'model' => $request->goldItem->model,
                        'kind' => $request->goldItem->kind,
                        'weight' => $request->goldItem->weight,
                        'gold_color' => $request->goldItem->gold_color,
                        'metal_type' => $request->goldItem->metal_type,
                        'metal_purity' => $request->goldItem->metal_purity,
                        'quantity' => $request->goldItem->quantity ?? 1,
                        'stones' => $request->goldItem->stones,
                        'talab' => $request->goldItem->talab,
                        'transfer_completed_at' => now()
                    ]);
                }
            }
        } else {
            // If rejected, reset the status
            if ($request->type == 'pound') {
                if ($request->pound) {
                    $request->pound->update(['status' => 'active']);
                }
            } else {
                if ($request->goldItem) {
                    $request->goldItem->update(['status' => 'available']);
                }
            }
        }

        $request->update(['status' => $status]);
        
        Log::info('Transfer request completed', [
            'request_id' => $requestId,
            'final_status' => $status
        ]);
        
        // Delete the transfer request after processing
        $request->delete();
        
        return $request;
    });
}

    public function getPendingTransfers()
    {
        $user = Auth::user();
        
        $incomingRequests = TransferRequest::with(['goldItem', 'pound'])
            ->where('to_shop_name', $user->shop_name)
            ->where('status', 'pending')
            ->get();

        $outgoingRequests = TransferRequest::with(['goldItem', 'pound'])
            ->where('from_shop_name', $user->shop_name)
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