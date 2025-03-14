<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldItemsAvg extends Model
{
    use HasFactory;
    protected $table = 'gold_items_avg';  // Or whatever your actual table name is

    protected $fillable = [
        'model', 'stones_weight'
    ];

    public function model()
    {
        return $this->belongsTo(Model::class, 'model', 'model');
    }
}
