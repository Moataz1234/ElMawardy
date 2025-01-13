<?php

namespace App\Observers;

use App\Models\GoldItemsAvg;
use App\Models\Models;
use Illuminate\Support\Facades\Log;

class GoldItemsAvgObserver
{
    // Called when a new record is created
    public function created(GoldItemsAvg $goldItemsAvg)
    {
        $this->updateModelsTable($goldItemsAvg->model);
    }

    // Called when a record is updated
    public function updated(GoldItemsAvg $goldItemsAvg)
    {
        $this->updateModelsTable($goldItemsAvg->model);
    }

    // Called when a record is deleted
    // public function deleted(GoldItemsAvg $goldItemsAvg)
    // {
    //     $this->updateModelsTable($goldItemsAvg->model);
    // }

    // Calculate the average stones_weight and update the models table
    protected function updateModelsTable($model)
    {
        Log::info('Updating average_of_stones for model:', ['model' => $model]);
    
        $averageStonesWeight = GoldItemsAvg::where('model', $model)
        ->whereNotNull('stones_weight') // Exclude NULL values
        ->avg('stones_weight');
    
        Log::info('Calculated average stones_weight:', ['average' => $averageStonesWeight]);
        
        Models::where('model', $model)
        ->update(['average_of_stones' => $averageStonesWeight ?? 0]); // Default to 0 if NULL
    
        Log::info('Updated models table for model:', ['model' => $model]);
    }
}