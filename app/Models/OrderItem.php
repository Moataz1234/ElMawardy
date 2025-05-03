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
        'order_kind',
        'item_type',  // Changed from order_fix_type
        'model',           // New field
        'serial_number',   // New field
        'weight',
        'image_link',
        'order_details',
        'order_type',
        'cost',            // New column
        'gold_weight',     // New column
        'new_barcode',     // New column
        'new_diamond_number' // New column
        // 'quantity',
        // 'gold_color',
        // 'ring_size',
    ];

    /**
     * Relationship to the Order model
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

