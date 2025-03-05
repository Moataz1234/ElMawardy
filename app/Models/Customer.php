<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'phone_number',
        'email',
        'payment_method',
    ];

    public function goldItemsSold()
    {
        return $this->hasMany(GoldItemSold::class, 'customer_id');
    }
}
