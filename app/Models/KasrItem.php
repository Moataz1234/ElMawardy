<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasrItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kasr_sale_id',
        'kind',
        'metal_purity',
        'weight',
        'net_weight',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'net_weight' => 'decimal:2',
    ];

    public function kasrSale()
    {
        return $this->belongsTo(KasrSale::class);
    }
} 