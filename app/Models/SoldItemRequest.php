<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoldItemRequest extends Model
{
    protected $fillable = [
        'item_serial_number',
        'shop_name',
        'approver_shop_name',
        'status',
        'customer_id',
        'price',
        'payment_method'

    ];
    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'item_serial_number', 'serial_number');
    }
    public function soldItem()
    {
        return $this->belongsTo(GoldItemSold::class, 'gold_item_sold_id');
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by', 'name'); // Assuming 'name' is unique
    }
    
}
