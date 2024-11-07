<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    use HasFactory;
    // Specify the table name explicitly
    protected $table = 'models';
    protected $fillable = ['model', 'category'];

    public function goldItems()
    {
        return $this->hasMany(GoldItem::class, 'model', 'model');
    }
    public function categoryPrice()
{
    return $this->belongsTo(CategoryPrice::class, 'category', 'category');
}
}
