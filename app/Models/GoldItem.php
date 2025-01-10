<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'model',
        'serial_number',
        'kind',
        'shop_name',
        'shop_id',
        'weight',
        'gold_color',
        'metal_type',
        'metal_purity',
        'quantity',
        'stones',
        'talab',
        'status',
        'rest_since',
        'created_at',
        'updated_at',
    ];
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'shop_name', 'name');
    }

    public function transferRequests()
    {
        return $this->hasMany(TransferRequest::class);
    }
    public function outers()
    {
        return $this->hasMany(Outer::class, 'gold_serial_number', 'serial_number');
    }
    public function talabatCategory()
    {
        return $this->belongsTo(Talabat::class, 'talabat', 'model');
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
