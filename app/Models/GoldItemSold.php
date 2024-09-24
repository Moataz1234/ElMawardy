<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldItemSold extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming convention
    protected $table = 'gold_items_sold';

    // Define the fillable fields
    protected $fillable = [
        'link',
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
        'add_date',
        'source',
        'to_print',
        'price',
        'semi_or_no',
        'average_of_stones',
        'net_weight',
        'sold_date',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
}
