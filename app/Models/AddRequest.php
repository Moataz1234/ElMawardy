<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddRequest extends Model
{
    protected $fillable = [
        'serial_number', 'model', 'shop_id', 'shop_name', 'kind', 
        'gold_color', 'metal_type', 'metal_purity', 'quantity', 
        'weight', 'talab', 'status', 'rest_since', 

    ];
}
