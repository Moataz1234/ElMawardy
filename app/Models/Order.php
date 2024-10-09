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
        'order_details',
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
        'by_two',
        'status',  
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
