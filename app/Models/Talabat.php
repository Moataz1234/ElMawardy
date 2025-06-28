<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Talabat extends BaseModel
{
    use HasFactory;
    protected $table = 'talabat';

    protected $fillable = [
        'model',
        'scanned_image',
        'stars',
        'source',
        'first_production',
        'semi_or_no',
        'average_of_stones'
    ];

    public function goldItems()
    {
        return $this->hasMany(GoldItem::class, 'model', 'model');
    }

    // public function goldItemsAvg()
    // {
    //     return $this->hasOne(GoldItemsAvg::class, 'model', 'model');
    // }
    // public function categoryPrice()
    // {
    //     return $this->belongsTo(CategoryPrice::class, 'category', 'category');
    // }
}
