<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasrSaleComplete extends Model
{
    use HasFactory;

    protected $table = 'kasr_sales_complete';

    protected $fillable = [
        'original_kasr_sale_id',
        'customer_name',
        'customer_phone',
        'original_shop_name',
        'shop_name',
        'image_path',
        'offered_price',
        'order_date',
        'completion_date',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'completion_date' => 'date',
        'offered_price' => 'decimal:2',
    ];

    /**
     * Get the original kasr sale record
     */
    public function originalSale()
    {
        return $this->belongsTo(KasrSale::class, 'original_kasr_sale_id');
    }

    /**
     * Get the items for this kasr sale through the original sale
     */
    public function items()
    {
        return $this->hasManyThrough(
            KasrItem::class,
            KasrSale::class,
            'id', // Foreign key on KasrSale table
            'kasr_sale_id', // Foreign key on KasrItem table
            'original_kasr_sale_id', // Local key on KasrSaleComplete table
            'id' // Local key on KasrSale table
        );
    }
    
    /**
     * Helper methods to get total weight
     */
    public function getTotalWeight()
    {
        return $this->items->sum('weight');
    }
    
    public function getTotalNetWeight()
    {
        return $this->items->sum('net_weight');
    }
} 