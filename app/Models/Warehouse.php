<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'link', 'serial_number', 'shop_name', 'shop_id', 'kind', 'model', 'talab',
        'gold_color', 'stones', 'metal_type', 'metal_purity', 'quantity',
        'weight', 'rest_since', 'source', 'to_print', 'price', 'semi_or_no',
        'average_of_stones', 'net_weight', 'website'
    ];

    protected $casts = [
        'rest_since' => 'date',
        'to_print' => 'boolean',
        'quantity' => 'integer',
        'weight' => 'decimal:2',
        'price' => 'decimal:2',
        'average_of_stones' => 'decimal:2',
        'net_weight' => 'decimal:2',
    ];
}