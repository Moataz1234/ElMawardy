<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'order_number',
        'image_link',
        'order_details',
        'order_kind',
        'ring_size',
        'weight',
        'gold_color',
        'order_fix_type',
        'customer_name',
        'seller_name',
        'deposit',
        'rest_of_cost',
        'customer_phone',
        'order_date',
        'deliver_date',
        'payment_method',
        'by_customer',
        'by_shop' ,
        'status',  
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
