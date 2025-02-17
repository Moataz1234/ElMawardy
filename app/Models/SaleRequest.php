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
        'kind'
    ];
    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'item_serial_number', 'serial_number');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    // public function getPricePerGram()
    // {
    //     if ($this->item_type === 'pound') {
    //         return $this->weight ? round($this->price / $this->weight, 2) : 0;
    //     } else {
    //         return $this->goldItem && $this->goldItem->weight ? round($this->price / $this->goldItem->weight, 2) : 0;
    //     }
    // }
}
