<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldItemWeightHistory extends Model
{
    use HasFactory;

    protected $table = 'gold_item_weight_history';
    protected $fillable = [
        'gold_item_id',
        'user_id', // Add this line
        'weight_before',
        'weight_after',
    ];

}
