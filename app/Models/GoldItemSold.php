<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldItemSold extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming convention
    protected $table = 'gold_items_sold';
    
    // Define the fillable fields
    protected $fillable = [
        'serial_number',
        'shop_name',
        'shop_id',
        'kind',
        'model',
        'talab',
        'gold_color',
        'stones',
        'metal_type',
        'metal_purity',
        'quantity',
        'weight',
        'add_date',
        'price',
        'sold_date',
        'customer_id',
        'created_at',
        'updated_at',
        'stars',
        'source'

    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('sold_date', $date);
    }
    public function modelCategory()
    {
        return $this->belongsTo(Models::class, 'model', 'model');
    }

    public function modelDetails()
    {
        return $this->hasOne(GoldItemDetail::class, 'model', 'model');
    }
}
