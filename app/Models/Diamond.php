<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diamond extends Model
{
    use HasFactory;
    protected $table = 'diamond';  // Or whatever your actual table name is

    protected $fillable = [
        'code', 'kind', 'cost', 'calico1', 'weight1', 'calico2', 'number2', 'weight2', 
        'calico3', 'number3', 'weight3', 'calico4', 'number4', 'weight4', 'calico5', 
        'number5', 'weight5', 'calico6', 'number6', 'weight6', 'cost1', 'cost2', 'cost3', 'cost4', 
        'cost5', 'cost6','sta', 'model', 'workshop', 'tarkeeb', 'gela', 'banue', 'date', 'condition', 
        'selling_date', 'selling_price', 'shop', 'name', 'return', 'date_r'
    ];
}
