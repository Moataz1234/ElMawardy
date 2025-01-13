<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GoldItemsAvg;
use App\Models\Models;

class CalculateAverageStones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:average-stones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the average stones_weight for all models and update the models table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all unique models from the gold_items_avg table
        $models = GoldItemsAvg::select('model')
            ->groupBy('model')
            ->get();

        // Loop through each model and calculate the average stones_weight
        foreach ($models as $model) {
            $averageStonesWeight = GoldItemsAvg::where('model', $model->model)
                ->avg('stones_weight');

            // Update the models table with the calculated average
            Models::where('model', $model->model)
                ->update(['average_of_stones' => $averageStonesWeight ?? 0]);

            $this->info("Updated model {$model->model} with average stones_weight: {$averageStonesWeight}");
        }

        $this->info('Average stones_weight calculation completed.');
    }
}