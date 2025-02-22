<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_serial_number',
        'shop_name',
        'approver_shop_name',
        'status',
        'customer_id',
        'price',
        'payment_method',
        'customer_first_name',
        'customer_last_name',
        'customer_phone',
        'customer_address',
        'customer_email',
        'item_type',
        'weight',
        'purity',
        'kind',
        'related_item_serial' // Optional: Links pound to parent item when related
    ];

    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'item_serial_number', 'serial_number');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Optional relationship: Get associated pound (if this is an item with a pound)
    public function associatedPound()
    {
        return $this->hasOne(SaleRequest::class, 'related_item_serial', 'item_serial_number')
            ->where('item_type', 'pound');
    }

    // Optional relationship: Get parent item (if this is a pound linked to an item)
    public function parentItem()
    {
        return $this->belongsTo(SaleRequest::class, 'related_item_serial', 'item_serial_number')
            ->where('item_type', '!=', 'pound');
    }

    // Helper to check if this is a standalone request
    public function isStandalone()
    {
        return !$this->related_item_serial && !$this->parentItem;
    }
}