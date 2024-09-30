<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'gold_buy',
        'gold_sell',
        'percent',
        'dollar_price',
        'gold_with_work',
        'gold_in_diamond',
        'shoghl_agnaby'
    ];
}
