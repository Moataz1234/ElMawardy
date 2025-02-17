<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPoundInventory extends Model
{
    protected $table = 'gold_pounds_inventory';

    protected $fillable = [
        'gold_pound_id',
        'serial_number',
        'shop_name',
        'type',
        'quantity',
        'weight',
        'purity',
        'status'
    ];
    public function goldPound()
    {
        return $this->belongsTo(GoldPound::class, 'gold_pound_id', 'id');
    }
    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'serial_number', 'serial_number');
    }
    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_name', 'shop_name');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
