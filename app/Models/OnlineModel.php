<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'notes'
    ];

    /**
     * Get the model that corresponds to this online model.
     */
    public function model()
    {
        return $this->belongsTo(Models::class, 'sku', 'SKU');
    }
}
