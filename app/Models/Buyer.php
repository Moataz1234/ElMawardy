<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'payment_method',
        'gold_item_sold_id',
    ];

    public function goldItemSold()
    {
        return $this->belongsTo(GoldItemSold::class);
    }
}
