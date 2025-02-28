<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaboratoryInput extends Model
{
    protected $fillable = [
        'operation_id',
        'weight',
        'purity',
        'input_date',
    ];

    protected $casts = [
        'input_date' => 'datetime',
        'weight' => 'decimal:3',
        'purity' => 'integer',
    ];

    public function operation()
    {
        return $this->belongsTo(LaboratoryOperation::class, 'operation_id');
    }

} 