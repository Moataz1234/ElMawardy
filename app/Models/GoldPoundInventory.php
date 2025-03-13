<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPoundInventory extends Model
{
    protected $table = 'gold_pounds_inventory';

    protected $fillable = [
        'serial_number',
        'gold_pound_id',
        'shop_name',
        'status',
        'type',
        'weight',
        'purity',
        'quantity',
        'related_item_serial'
    ];

    public function goldPound()
    {
        return $this->belongsTo(GoldPound::class, 'gold_pound_id');
    }

    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'related_item_serial', 'serial_number');
    }

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_name', 'shop_name');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleRequest()
    {
        return $this->hasOne(SaleRequest::class, 'item_serial_number', 'serial_number');
    }
}