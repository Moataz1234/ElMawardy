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

    public function isPound()
    {
        return in_array($this->semi_or_no, ['1 pound', '1/2 pound', '1/4 pound']);
    }

    public function getPoundId()
    {
        switch ($this->semi_or_no) {
            case '1 pound':
                return 1;
            case '1/2 pound':
                return 2;
            case '1/4 pound':
                return 3;
            default:
                return null;
        }
    }
}
