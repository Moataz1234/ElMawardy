<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $table = 'order_items';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'order_id',   // Foreign key for linking to the orders table
        'quantity',
        'order_kind',
        'item_type',  // Changed from order_fix_type
        'ring_size',
        'weight',
        'gold_color',
        'image_link',
        'order_details',
        'order_type'
    ];

    /**
     * Relationship to the Order model
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

