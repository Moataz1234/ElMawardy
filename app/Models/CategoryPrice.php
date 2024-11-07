<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryPrice extends Model
{
    use HasFactory;

    protected $table = 'category_prices';

    protected $fillable = ['category', 'price'];

    public function models()
    {
        return $this->hasMany(Model::class, 'category', 'category');
    }
}
