<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedItemHistory extends Model
{
    protected $table = 'deleted_items_history'; // Specify the correct table name

    protected $fillable = [
        'item_id', 'deleted_by', 'serial_number', 'shop_name', 
        'kind', 'model', 'gold_color', 'metal_purity', 
        'weight', 'deletion_reason', 'deleted_at'
    ];

}
