<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddRequest;
use App\Models\DeletedItemHistory;
use App\Models\GoldItem;
use App\Models\GoldItemSold;
use App\Models\GoldPoundInventory;
use App\Models\GoldPoundSold;
use App\Models\OrderItem;
use App\Models\Outer;
use App\Models\PoundRequest;
use App\Models\SaleRequest;
use App\Models\SoldItemRequest;
use App\Models\TransferRequestHistory;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SerialNumberTrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function search(Request $request)
    {
        $serial = $request->input('serial_number');
        
        if (empty($serial)) {
            return redirect()->route('tracking.index')->with('error', 'Please enter a serial number');
        }

        // Format serial number - handle searches without G- prefix
        $formattedSerial = $this->formatSerialNumber($serial);
        
        // Collect all appearances of the serial number across all models
        $trackingData = $this->collectTrackingData($formattedSerial);
        
        // Sort by created_at timestamp, newest first
        $trackingData = $trackingData->sortByDesc('created_at');
        
        return view('tracking.results', [
            'trackingData' => $trackingData,
            'serial' => $formattedSerial
        ]);
    }
    
    /**
     * API endpoint to get tracking data for a serial number
     */
    public function apiSearch(Request $request)
    {
        $serial = $request->input('serial_number');
        
        if (empty($serial)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a serial number'
            ], 400);
        }
        
        // Format serial number - handle searches without G- prefix
        $formattedSerial = $this->formatSerialNumber($serial);
        
        // Collect all appearances of the serial number across all models
        $trackingData = $this->collectTrackingData($formattedSerial);
        
        // Sort by created_at timestamp, newest first
        $trackingData = $trackingData->sortByDesc('created_at');
        
        return response()->json([
            'success' => true,
            'serial_number' => $formattedSerial,
            'count' => $trackingData->count(),
            'data' => $trackingData
        ]);
    }

    /**
     * Format the serial number by adding G- prefix if it's missing and is numeric
     * 
     * @param string $serial The serial number to format
     * @return string The formatted serial number
     */
    private function formatSerialNumber($serial)
    {
        // Clean up the input first
        $serial = trim($serial);
        
        // If already has G- prefix, return as is
        if (strpos($serial, 'G-') === 0) {
            return $serial;
        }
        
        // If it's numeric, add G- prefix
        if (is_numeric($serial)) {
            return 'G-' . $serial;
        }
        
        // Otherwise, return as is
        return $serial;
    }

    private function collectTrackingData($serial)
    {
        $trackingData = new Collection();
        $searchValues = [$serial];
        
        // If the serial has a G- prefix, also search without it
        if (strpos($serial, 'G-') === 0) {
            $searchValues[] = substr($serial, 2);
        } 
        // If the serial doesn't have a G- prefix, also search with it
        else if (is_numeric($serial)) {
            $searchValues[] = 'G-' . $serial;
        }
        
        // GoldItem
        $goldItems = GoldItem::whereIn('serial_number', $searchValues)->get();
        foreach ($goldItems as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Gold Items',
                'status' => $item->status,
                'details' => [
                    'Model' => $item->model,
                    'Kind' => $item->kind,
                    'Weight' => $item->weight,
                    'Shop' => $item->shop_name
                ]
            ]);
        }
        
        // GoldItemSold - Add add_date data for sold items
        $goldItemsSold = GoldItemSold::whereIn('serial_number', $searchValues)->get();
        foreach ($goldItemsSold as $item) {
            // If add_date is null, try to find a reasonable date
            $addDate = $item->add_date;
            if (!$addDate) {
                $addDate = $this->findAddDateForSoldItem($item->serial_number);
            }
            
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Sold Gold Items',
                'status' => 'Sold',
                'details' => [
                    'Model' => $item->model,
                    'Kind' => $item->kind,
                    'Weight' => $item->weight,
                    'Shop' => $item->shop_name,
                    'Sold Date' => $item->sold_date,
                    'Add Date' => $addDate ? date('Y-m-d', strtotime($addDate)) : 'N/A',
                    'Price' => $item->price,
                    'Time in Inventory' => $this->calculateTimeInInventory($addDate, $item->sold_date)
                ]
            ]);
        }
        
        // AddRequest
        $addRequests = AddRequest::whereIn('serial_number', $searchValues)->get();
        foreach ($addRequests as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Add Requests',
                'status' => $item->status,
                'details' => [
                    'Model' => $item->model,
                    'Kind' => $item->kind,
                    'Shop' => $item->shop_name
                ]
            ]);
        }
        
        // Warehouse
        $warehouseItems = Warehouse::whereIn('serial_number', $searchValues)->get();
        foreach ($warehouseItems as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Warehouse',
                'status' => $item->status,
                'details' => [
                    'Model' => $item->model,
                    'Kind' => $item->kind,
                    'Weight' => $item->weight,
                    'Shop' => $item->shop_name
                ]
            ]);
        }
        
        // DeletedItemHistory
        $deletedItems = DeletedItemHistory::whereIn('serial_number', $searchValues)->get();
        foreach ($deletedItems as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Deleted Items History',
                'status' => 'Deleted',
                'details' => [
                    'Model' => $item->model,
                    'Kind' => $item->kind,
                    'Shop' => $item->shop_name,
                    'Deleted By' => $item->deleted_by,
                    'Deletion Reason' => $item->deletion_reason,
                    'Deleted At' => $item->deleted_at
                ]
            ]);
        }
        
        // GoldPoundInventory
        $goldPounds = GoldPoundInventory::where(function($query) use ($searchValues) {
            $query->whereIn('serial_number', $searchValues)
                  ->orWhereIn('related_item_serial', $searchValues);
        })->get();
        
        foreach ($goldPounds as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Gold Pound Inventory',
                'status' => $item->status,
                'details' => [
                    'Type' => $item->type,
                    'Weight' => $item->weight,
                    'Purity' => $item->purity,
                    'Shop' => $item->shop_name,
                    'Related Item' => $item->related_item_serial
                ]
            ]);
        }
        
        // GoldPoundSold
        $goldPoundsSold = GoldPoundSold::whereIn('serial_number', $searchValues)->get();
        foreach ($goldPoundsSold as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Sold Gold Pounds',
                'status' => 'Sold',
                'details' => [
                    'Shop' => $item->shop_name,
                    'Price' => $item->price
                ]
            ]);
        }
        
        // OrderItem
        $orderItems = OrderItem::whereIn('serial_number', $searchValues)->get();
        foreach ($orderItems as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Order Items',
                'status' => $item->order_type,
                'details' => [
                    'Order ID' => $item->order_id,
                    'Model' => $item->model,
                    'Weight' => $item->weight,
                    'Cost' => $item->cost
                ]
            ]);
        }
        
        // Outer
        $outerItems = Outer::whereIn('gold_serial_number', $searchValues)->get();
        foreach ($outerItems as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Outer Records',
                'status' => $item->is_returned ? 'Returned' : 'Not Returned',
                'details' => [
                    'Name' => $item->first_name . ' ' . $item->last_name,
                    'Phone' => $item->phone_number,
                    'Reason' => $item->reason
                ]
            ]);
        }
        
        // PoundRequest
        $poundRequests = PoundRequest::whereIn('serial_number', $searchValues)->get();
        foreach ($poundRequests as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Pound Requests',
                'status' => $item->status,
                'details' => [
                    'Type' => $item->type,
                    'Weight' => $item->weight,
                    'Purity' => $item->custom_purity,
                    'Shop' => $item->shop_name
                ]
            ]);
        }
        
        // SaleRequest
        $saleRequests = SaleRequest::where(function($query) use ($searchValues) {
            $query->whereIn('item_serial_number', $searchValues)
                  ->orWhereIn('related_item_serial', $searchValues);
        })->get();
        
        foreach ($saleRequests as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Sale Requests',
                'status' => $item->status,
                'details' => [
                    'Item Type' => $item->item_type,
                    'Weight' => $item->weight,
                    'Price' => $item->price,
                    'Shop' => $item->shop_name,
                    'Approver Shop' => $item->approver_shop_name,
                    'Payment Method' => $item->payment_method,
                    'Related Item' => $item->related_item_serial
                ]
            ]);
        }
        // TransferRequestHistory
        $transferHistory = TransferRequestHistory::whereIn('serial_number', $searchValues)->get();
        foreach ($transferHistory as $item) {
            $trackingData->push([
                'created_at' => $item->created_at,
                'source' => 'Transfer History',
                'status' => $item->status,
                'details' => [
                    'Model' => $item->model,
                    'Kind' => $item->kind,
                    'Weight' => $item->weight,
                    'From Shop' => $item->from_shop_name,
                    'To Shop' => $item->to_shop_name,
                    'Completed At' => $item->transfer_completed_at
                ]
            ]);
        }
        
        return $trackingData;
    }
    
    /**
     * Calculate the time an item spent in inventory
     * 
     * @param string|null $addDate The date when the item was added to inventory
     * @param string|null $soldDate The date when the item was sold
     * @return string A formatted string representing the time in inventory
     */
    private function calculateTimeInInventory($addDate, $soldDate)
    {
        if (!$addDate || !$soldDate) {
            return 'Unknown';
        }
        
        $addDateTime = strtotime($addDate);
        $soldDateTime = strtotime($soldDate);
        
        if (!$addDateTime || !$soldDateTime) {
            return 'Invalid dates';
        }
        
        // Calculate the difference in days
        $diffInSeconds = $soldDateTime - $addDateTime;
        $diffInDays = round($diffInSeconds / (60 * 60 * 24));
        
        if ($diffInDays < 0) {
            return 'Data error (negative time)';
        }
        
        if ($diffInDays == 0) {
            return 'Same day';
        }
        
        if ($diffInDays < 30) {
            return $diffInDays . ' days';
        }
        
        if ($diffInDays < 365) {
            $months = round($diffInDays / 30);
            return $months . ' month' . ($months > 1 ? 's' : '');
        }
        
        $years = floor($diffInDays / 365);
        $remainingDays = $diffInDays % 365;
        $months = round($remainingDays / 30);
        
        $result = $years . ' year' . ($years > 1 ? 's' : '');
        if ($months > 0) {
            $result .= ', ' . $months . ' month' . ($months > 1 ? 's' : '');
        }
        
        return $result;
    }

    /**
     * Try to find a reasonable add_date for a sold item if it's not available
     * 
     * @param string $serialNumber The serial number of the sold item
     * @return string|null A reasonable add_date if found, null otherwise
     */
    private function findAddDateForSoldItem($serialNumber)
    {
        // Check AddRequest table
        $addRequest = AddRequest::where('serial_number', $serialNumber)
            ->orderBy('created_at', 'asc')
            ->first();
        
        if ($addRequest) {
            return $addRequest->created_at;
        }
        
        // Check TransferRequestHistory for the earliest transfer
        $transferHistory = TransferRequestHistory::where('serial_number', $serialNumber)
            ->orderBy('created_at', 'asc')
            ->first();
        
        if ($transferHistory) {
            return $transferHistory->created_at;
        }
        
        return null;
    }
} 