<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'name',
        'address',
        // Add other fields as necessary
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public static function getIdByName($shopName)
    {
        return static::where('name', $shopName)->value('id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'shop_name', 'name');
    }
}
