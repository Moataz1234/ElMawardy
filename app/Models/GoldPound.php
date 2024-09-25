<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldPound extends Model
{
    use HasFactory;

    protected $table = 'gold_pounds';

    protected $fillable = [
        'kind',
        'weight',
        'purity',
        'quantity',
        'description',
    ];
}
