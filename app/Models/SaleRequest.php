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
        'price'
    ];
    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'item_serial_number', 'serial_number');
    }
}
