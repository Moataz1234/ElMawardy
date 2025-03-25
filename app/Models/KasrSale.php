<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasrSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'shop_name',
        'weight',
        'kind',
        'metal_purity',
        // 'metal_type',
        'image_path',
        'offered_price',
        'order_date',
        'status',
        // 'notes',
        'item_type',
    ];

    protected $casts = [
        'order_date' => 'date',
        'weight' => 'decimal:2',
        'offered_price' => 'decimal:2',
    ];

    public function shop()
    {
        return $this->belongsTo(User::class, 'shop_name', 'shop_name');
    }
} 