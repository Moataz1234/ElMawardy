<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldItemDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'model', 'scanned_image', 'product_image', 'first_production', 
        'kind', 'talab', 'stones', 'rest_since', 'source', 
        'to_print', 'stars', 'semi_or_no', 'net_weight', 'average_of_stones'
    ];


    public function mainProducts()
    {
        return $this->belongsTo(GoldItem::class, 'model', 'model');
    }

    public function stones()
    {
        return $this->belongsTo(AVG_Stone::class, 'model', 'model');
    }
}
