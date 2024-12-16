<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AVG_Stone extends Model
{
    protected $fillable = [
        'model', 'stones_weight'
    ];

    public function modelDetails()
    {
        return $this->hasOne(GoldItemDetail::class, 'model', 'model');
    }
}
