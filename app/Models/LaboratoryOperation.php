<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LaboratoryInput;
use App\Models\LaboratoryOutput;

class LaboratoryOperation extends Model
{
    protected $fillable = [
        'operation_number',
        'operation_date',
        'total_input_weight',
        'total_output_weight',
        'loss',
        'silver_weight',
        'operation_cost',
        'status',
    ];

    protected $casts = [
        'operation_date' => 'datetime',
        'total_input_weight' => 'decimal:3',
        'total_output_weight' => 'decimal:3',
        'loss' => 'decimal:3',
        'silver_weight' => 'decimal:3',
        'operation_cost' => 'decimal:2',
    ];

    public function inputs()
    {
        return $this->hasMany(LaboratoryInput::class, 'operation_id');
    }

    public function outputs()
    {
        return $this->hasMany(LaboratoryOutput::class, 'operation_id');
    }
}