<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaboratoryOutput extends Model
{
    protected $fillable = [
        'operation_id',
        'weight',
        'purity',
        'destination_type',
        'destination_id',
        'output_date',
    ];

    protected $casts = [
        'output_date' => 'datetime',
        'weight' => 'decimal:3',
        'purity' => 'integer',
    ];

    public function operation()
    {
        return $this->belongsTo(LaboratoryOperation::class, 'operation_id');
    }

} 