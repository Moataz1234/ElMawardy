<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoundRequest extends Model
{
    protected $table = 'add_pound_requests';

    protected $fillable = [
        'serial_number',
        'gold_pound_id',
        'shop_name',
        'type',
        'weight',
        // 'custom_weight',
        'custom_purity',
        'quantity',
        'status',
        'image_path'
    ];

    public function goldPound()
    {
        return $this->belongsTo(GoldPound::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_name', 'name');
    }
} 