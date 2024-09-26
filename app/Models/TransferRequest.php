<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferRequest extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'gold_item_id',
        'from_shop_id',
        'to_shop_id',
        'status',
    ];

    /**
     * Get the gold item that is being transferred.
     */
    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'gold_item_id');
    }

    /**
     * Get the shop from which the item is being transferred.
     */
    public function fromShop()
    {
        return $this->belongsTo(Shop::class, 'from_shop_id');
    }

    /**
     * Get the shop to which the item is being transferred.
     */
    public function toShop()
    {
        return $this->belongsTo(Shop::class, 'to_shop_id');
    }
}
