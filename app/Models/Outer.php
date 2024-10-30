<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'phone_number', 'reason', 'gold_serial_number','is_returned'
    ];

    /**
     * Define the inverse of the relationship with GoldItem.
     */
    public function goldItem()
    {
        return $this->belongsTo(GoldItem::class, 'gold_serial_number', 'serial_number');
    }
}
