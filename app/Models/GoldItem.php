<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'link', 'serial_number', 'shop_name', 'shop_id', 'kind', 'model', 'talab', 
        'gold_color', 'stones', 'metal_type', 'metal_purity', 'quantity', 
        'weight', 'rest_since', 'source', 'to_print', 'price', 'semi_or_no', 
        'average_of_stones', 'net_weight'
    ];
}