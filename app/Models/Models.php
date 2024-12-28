<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Models extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'model', 'SKU', 'scanned_image', 'website_image', 
        'stars', 'source', 'first_production', 
        'semi_or_no', 'average_of_stones'
    ];

    public function goldItems()
    {
        return $this->hasMany(GoldItem::class, 'model', 'model');
    }

    public function goldItemsAvg()
    {
        return $this->hasOne(GoldItemsAvg::class, 'model', 'model');
    }
    public function categoryPrice()
    {
        return $this->belongsTo(CategoryPrice::class, 'category', 'category');
    }
}
