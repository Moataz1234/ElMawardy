<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPoundSold extends Model
{
    protected $table = 'gold_pounds_sold';

    protected $fillable = [
        'serial_number',
        'gold_pound_id',
        'shop_name',
        'price',
        'customer_id'
    ];

    public function poundInventory()
    {
        return $this->belongsTo(GoldPoundInventory::class, 'serial_number', 'serial_number');
    }

    public function goldPound()
    {
        return $this->belongsTo(GoldPound::class, 'gold_pound_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
} 