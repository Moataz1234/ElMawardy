<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'serial_number',
        'shop_name',
        'shop_id',
        'kind',
        'model',
        'talab',
        'gold_color',
        'stones',
        'metal_type',
        'metal_purity',
        'quantity',
        'weight',
        'status'
        // 'rest_since',
  ];

    protected $casts = [
        // 'rest_since' => 'date',
        'quantity' => 'integer',
        'weight' => 'decimal:2',
       
    ];
}
