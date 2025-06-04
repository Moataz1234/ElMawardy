<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForProduction extends Model
{
    use HasFactory;

    protected $table = 'for_production';
    
    protected $fillable = [
        'model',
        'quantity',
        'not_finished',
        'order_date'
    ];

    protected $casts = [
        'order_date' => 'date'
    ];
}
