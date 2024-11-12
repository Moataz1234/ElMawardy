<?php
namespace App\Services;

use App\Models\Outer;
use Illuminate\Http\Request;
use App\Models\GoldItem;

class OuterService
{
    public function createOuter(Request $request)
    {
        return Outer::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_number' => $request->input('phone_number'),
            'reason' => $request->input('reason'),
            'gold_serial_number' => $request->input('gold_serial_number'),
            'is_returned' => false,
        ]);
    }

    public function markAsReturned($serialNumber)
    {
        $outer = Outer::where('gold_serial_number', $serialNumber)->first();
        if ($outer) {
            $outer->is_returned = true;
            $outer->save();
        }
    }

    public function toggleReturn($serialNumber)
    {
        $item = GoldItem::where('serial_number', $serialNumber)->first();
        if ($item) {
            $outer = Outer::where('gold_serial_number', $serialNumber)->first();
            if ($outer) {
                $outer->is_returned = false;
                $outer->save();
            } else {
                return view('Shops.Gold.outerform', compact('serial_number'));
            }
        }
        return redirect()->back()->with('error', 'Item not found.');
    }
    public function toggleOuterStatus(string $serialNumber): array
    {
        $item = GoldItem::where('serial_number', $serialNumber)->first();
        
        if (!$item) {
            return [
                'redirect' => false,
                'status' => 'error',
                'message' => 'Item not found.'
            ];
        }

        $outer = Outer::where('gold_serial_number', $serialNumber)->first();
        
        if ($outer) {
            $outer->update(['is_returned' => false]);
            return [
                'redirect' => false,
                'status' => 'success',
                'message' => 'Item status updated to Outer.'
            ];
        }

        return ['redirect' => true];
    }
}