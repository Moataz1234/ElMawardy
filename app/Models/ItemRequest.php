<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    protected $fillable = [
        'item_id',
        'admin_id',
        'shop_name',
        'status'
    ];

    public function item()
    {
        return $this->belongsTo(GoldItem::class, 'item_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}