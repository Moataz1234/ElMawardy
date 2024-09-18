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
        'number5', 'weight5', 'calico6', 'number6', 'weight6', 'calico7', 'number7', 
        'weight7', 'calico8', 'number8', 'weight8', 'calico9', 'number9', 'weight9', 
        'calico10', 'number10', 'weight10', 'calico11', 'number11', 'weight11', 
        'calico12', 'number12', 'weight12', 'cost1', 'cost2', 'cost3', 'cost4', 
        'cost5', 'cost6', 'cost7', 'cost8', 'cost9', 'cost10', 'cost11', 'cost12',
        'sta', 'model', 'workshop', 'tarkeeb', 'gela', 'banue', 'date', 'condition', 
        'selling_date', 'selling_price', 'shop', 'name', 'return', 'date_r', 'details',
        'image_path', 'certificate_code', 'daftar_number'
    ];
}
