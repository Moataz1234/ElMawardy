<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasrSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'shop_name',
        'image_path',
        'offered_price',
        'order_date',
        'status',
        'item_type',
    ];

    protected $casts = [
        'order_date' => 'date',
        'offered_price' => 'decimal:2',
    ];

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_name', 'shop_name');
    }

    public function items()
    {
        return $this->hasMany(KasrItem::class);
    }
    
    // Helper methods to get total weight
    public function getTotalWeight()
    {
        return $this->items->sum('weight');
    }
    
    public function getTotalNetWeight()
    {
        return $this->items->sum('net_weight');
    }
} 