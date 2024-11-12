<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'gold_item_id',
        'from_shop_name',  // Changed from from_shop_id
        'to_shop_name',    // Changed from to_shop_id
        'status',
    ];

    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'gold_item_id');
    }

    // Update relationships to use User model instead of Shop
    public function fromShop()
    {
        return $this->belongsTo(User::class, 'from_shop_name', 'shop_name');
    }

    public function toShop()
    {
        return $this->belongsTo(User::class, 'to_shop_name', 'shop_name');
    }
}
