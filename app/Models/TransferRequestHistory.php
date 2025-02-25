<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferRequestHistory extends Model
{
    use HasFactory;

    protected $table = 'transfer_requests_history';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'transfer_completed_at' => 'datetime'
    ];

    protected $fillable = [
        'from_shop_name',
        'to_shop_name',
        'status',
        'serial_number',
        'model',
        'kind',
        'weight',
        'gold_color',
        'metal_type',
        'metal_purity',
        'quantity',
        'stones',
        'talab',
        'transfer_completed_at',
        'item_sold_at',
        'stars',
        'scanned_image'
    ];

    protected $dates = [
        'transfer_completed_at',
        'item_sold_at',
        'created_at',
        'updated_at'
    ];

    public function fromShop()
    {
        return $this->belongsTo(User::class, 'from_shop_name', 'shop_name');
    }

    public function toShop()
    {
        return $this->belongsTo(User::class, 'to_shop_name', 'shop_name');
    }

    public function modelDetails()
    {
        return $this->belongsTo(Models::class, 'model', 'model');
    }
} 